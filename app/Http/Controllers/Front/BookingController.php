<?php

namespace App\Http\Controllers\Front;
use App\Models\Customer;
use App\Models\Order;
use App\Models\OrderDetail;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Auth;
use Illuminate\Support\Facades\DB; 
use PayPalCheckoutSdk\Core\PayPalHttpClient;
use PayPalCheckoutSdk\Core\SandboxEnvironment;
use PayPalCheckoutSdk\Core\ProductionEnvironment;
use PayPalCheckoutSdk\Orders\OrdersGetRequest;
use PayPalCheckoutSdk\Orders\OrdersCaptureRequest;

class BookingController extends Controller
{
    public function cart_submit(Request $request)
    {
         $request->validate([
            'room_id' => 'required',
            'checkin_checkout' => 'required',
            'adult' => 'required',
        ]);

        $dates = explode(' - ', $request->checkin_checkout);
        $checkin_date = $dates[0];
        $checkout_date = $dates[1];

        session()->push('cart_room_id', $request->room_id);
        session()->push('cart_checkin_date', $checkin_date);
        session()->push('cart_checkout_date', $checkout_date);
        session()->push('cart_adult', $request->adult);
        session()->push('cart_children', $request->children);

        return redirect()->back()->with('success', 'Room is added to the cart Successfully');
        
    }

    public function cart_view()
    {
        return view('front.cart');
    }

   public function cart_delete($id)
   {
    // Sécurise les récupérations de session avec valeur par défaut []
    $arr_cart_room_id = session()->get('cart_room_id', []);
    $arr_cart_checkin_date = session()->get('cart_checkin_date', []);
    $arr_cart_checkout_date = session()->get('cart_checkout_date', []);
    $arr_cart_adult = session()->get('cart_adult', []);
    $arr_cart_children = session()->get('cart_children', []);

    // Si le panier est vide, on redirige simplement sans planter
    if (empty($arr_cart_room_id)) {
        return redirect()->back()->with('error', 'No items found in cart.');
    }

    // On vide le panier pour le reconstruire sans l’élément supprimé
    session()->forget('cart_room_id');
    session()->forget('cart_checkin_date');
    session()->forget('cart_checkout_date');
    session()->forget('cart_adult');
    session()->forget('cart_children');

    // On reboucle pour recréer le panier sans la chambre à supprimer
    for ($i = 0; $i < count($arr_cart_room_id); $i++) {
        if ($arr_cart_room_id[$i] != $id) {
            session()->push('cart_room_id', $arr_cart_room_id[$i]);
            session()->push('cart_checkin_date', $arr_cart_checkin_date[$i]);
            session()->push('cart_checkout_date', $arr_cart_checkout_date[$i]);
            session()->push('cart_adult', $arr_cart_adult[$i]);
            session()->push('cart_children', $arr_cart_children[$i]);
        }
    }

    return redirect()->back()->with('success', 'Cart item deleted successfully');
   }

   public function checkout()
   {
      if(!Auth::guard('customer')->check()) {
        return redirect()->route('cart')->with('error', 'You must login in order to checkout');
      }

    
    
       if (!session()->has('cart_room_id') || empty(session('cart_room_id'))) {
          return redirect()->route('cart')->with('error', 'There is no item in the cart');
       }
    
      return view('front.checkout');
    }

    public function payment(Request $request)
    {
        if(!Auth::guard('customer')->check()) {
          return redirect()->route('cart')->with('error', 'You must login in order to checkout');
        }

        if (!session()->has('cart_room_id') || empty(session('cart_room_id'))) {
          return redirect()->route('cart')->with('error', 'There is no item in the cart');
        }
        
        
          $request->validate([
            'billing_name' => 'required',
            'billing_email' => 'required|email',
            'billing_phone' => 'required',
            'billing_country' => 'required',
            'billing_address' => 'required',
            'billing_state' => 'required',
            'billing_city' => 'required',
            'billing_zip' => 'required',
        ]);
        
        
        
        session()->put('billing_name', $request->billing_name);
        session()->put('billing_email', $request->billing_email);
        session()->put('billing_phone', $request->billing_phone);
        session()->put('billing_country', $request->billing_country);
        session()->put('billing_address', $request->billing_address);
        session()->put('billing_state', $request->billing_state);
        session()->put('billing_city', $request->billing_city);
        session()->put('billing_zip', $request->billing_zip);
        
        
        return view('front.payment');
    }



  


     public function paypal(Request $request)
{
    $captureId = $request->query('capture_id');

    if (!$captureId) {
        return redirect()->route('payment.cancel')
            ->with('error', 'Aucun identifiant de paiement reçu.');
    }

    try {
        // 🔹 On vérifie que l’utilisateur est connecté
        if (!Auth::guard('customer')->check()) {
            return redirect()->route('cart')->with('error', 'Veuillez vous connecter pour continuer.');
        }

        // 🔹 Récupération du panier
        $cart_room_id = session()->get('cart_room_id', []);
        $cart_checkin_date = session()->get('cart_checkin_date', []);
        $cart_checkout_date = session()->get('cart_checkout_date', []);
        $cart_adult = session()->get('cart_adult', []);
        $cart_children = session()->get('cart_children', []);

        if (empty($cart_room_id)) {
            return redirect()->route('cart')->with('error', 'Panier vide.');
        }

        // 🔹 Calcul du total
        $total_price = 0;
        foreach ($cart_room_id as $i => $room_id) {
            $room = \DB::table('rooms')->find($room_id);
            if (!$room) continue;

            $d1 = explode('/', $cart_checkin_date[$i]);
            $d2 = explode('/', $cart_checkout_date[$i]);
            $checkin = strtotime("$d1[2]-$d1[1]-$d1[0]");
            $checkout = strtotime("$d2[2]-$d2[1]-$d2[0]");
            $days = ($checkout - $checkin) / 86400;
            $total_price += $room->price * $days;
        }

        // 🔹 Création de la commande (on considère le paiement comme déjà validé)
        $order = new \App\Models\Order();
        $order->customer_id = Auth::guard('customer')->user()->id;
        $order->order_no = strtoupper(uniqid('ORD-'));
        $order->transaction_id = $captureId;
        $order->payment_method = 'PayPal';
        $order->paid_amount = $total_price;
        $order->booking_date = now();
        $order->status = 'completed';
        $order->save();

        $order_id = $order->id;

        // 🔹 Détails
        foreach ($cart_room_id as $i => $room_id) {
            $room = \DB::table('rooms')->find($room_id);
            if (!$room) continue;

            $d1 = explode('/', $cart_checkin_date[$i]);
            $d2 = explode('/', $cart_checkout_date[$i]);
            $checkin = strtotime("$d1[2]-$d1[1]-$d1[0]");
            $checkout = strtotime("$d2[2]-$d2[1]-$d2[0]");
            $days = ($checkout - $checkin) / 86400;
            $subtotal = $room->price * $days;

            $detail = new \App\Models\OrderDetail();
            $detail->order_id = $order_id;
            $detail->room_id = $room_id;
            $detail->checkin_date = $cart_checkin_date[$i];
            $detail->checkout_date = $cart_checkout_date[$i];
            $detail->adult = $cart_adult[$i] ?? 1;
            $detail->children = $cart_children[$i] ?? 0;
            $detail->subtotal = $subtotal;
            $detail->save();
        }

        // 🔹 Nettoyage
        session()->forget(['cart_room_id', 'cart_checkin_date', 'cart_checkout_date', 'cart_adult', 'cart_children']);

        
         session()->forget([
            'billing_name',
            'billing_email',
            'billing_phone',
            'billing_country',
            'billing_address',
            'billing_state',
             'billing_city',
            'billing_zip'
        ]);

        
        
        // 🔹 Redirection
        return redirect()->route('payment.success')
            ->with('success', 'Paiement confirmé avec succès !');

    } catch (\Exception $e) {
        return redirect()->route('payment.cancel')
            ->with('error', 'Erreur : ' . $e->getMessage());
    }
}
















public function paymentSuccess()
{
    // Ici tu peux afficher la page de remerciement + infos sur la réservation
    return view('front.payment_success');
}

public function paymentCancel()
{
    // Affiche une page d'annulation ou d'erreur
    return view('front.payment_cancel');
}


}
