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
use App\Mail\Websitemail;
use Stripe\Stripe;
use Stripe\PaymentIntent;

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

           // Vérification disponibilité avant d’ajouter au panier(test)
       if (!$this->isRoomAvailable($request->room_id, $checkin_date, $checkout_date)) {
          return redirect()->back()->with('error', 'This room is not available for the selected dates.');
        }

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
        
        
        // 🔹 Calcul du total pour passer au Blade
    $cart_room_id = session()->get('cart_room_id', []);
    $cart_checkin_date = session()->get('cart_checkin_date', []);
    $cart_checkout_date = session()->get('cart_checkout_date', []);
    $total_price = 0;

    foreach($cart_room_id as $i => $room_id){
        $room = DB::table('rooms')->find($room_id);
        if(!$room) continue;

        $d1 = explode('/', $cart_checkin_date[$i]);
        $d2 = explode('/', $cart_checkout_date[$i]);
        $checkin = strtotime("$d1[2]-$d1[1]-$d1[0]");
        $checkout = strtotime("$d2[2]-$d2[1]-$d2[0]");
        $days = ($checkout - $checkin) / 86400;

        $total_price += $room->price * $days;
    }
       return view('front.payment', compact('total_price'));
  }



  




  public function paypal(Request $request)
{
    // 🔹 Récupération du capture_id envoyé depuis le frontend
    $captureId = $request->query('capture_id');

    if (!$captureId) {
        return redirect()->route('payment.cancel')
            ->with('error', 'No payment identifier received.');
    }

    // 🔹 Vérification que le client est connecté
    if (!Auth::guard('customer')->check()) {
        return redirect()->route('cart')->with('error', 'Please login to continue.');
    }

    try {
        // 🔹 Récupération du panier
        $cart_room_id = session()->get('cart_room_id', []);
        $cart_checkin_date = session()->get('cart_checkin_date', []);
        $cart_checkout_date = session()->get('cart_checkout_date', []);
        $cart_adult = session()->get('cart_adult', []);
        $cart_children = session()->get('cart_children', []);

        if (empty($cart_room_id)) {
            return redirect()->route('cart')->with('error', 'Cart is empty.');
        }

        // 🔹 Calcul du total payé
        $total_price = 0;
        foreach ($cart_room_id as $i => $room_id) {
            $room = DB::table('rooms')->find($room_id);
            if (!$room) continue;

            $d1 = explode('/', $cart_checkin_date[$i]);
            $d2 = explode('/', $cart_checkout_date[$i]);
            $checkin = strtotime("$d1[2]-$d1[1]-$d1[0]");
            $checkout = strtotime("$d2[2]-$d2[1]-$d2[0]");
            $days = ($checkout - $checkin) / 86400;

            $total_price += $room->price * $days;
        }

        // test
        foreach ($cart_room_id as $i => $room_id) {
          if (!$this->isRoomAvailable($room_id, $cart_checkin_date[$i], $cart_checkout_date[$i])) {
            return redirect()->route('cart')->with('error', 'One of the selected rooms is no longer available.');
          }
        }

        // 🔹 Création de la commande principale
        $order = new Order();
        $order->customer_id = Auth::guard('customer')->user()->id;
        $order->order_no = strtoupper(uniqid('ORD-'));
        $order->transaction_id = $captureId;
        $order->payment_method = 'PayPal';
        $order->paid_amount = $total_price;
        $order->booking_date = now();
        $order->status = 'completed';
        $order->save();

        // 🔹 Création des détails pour chaque chambre
        foreach ($cart_room_id as $i => $room_id) {
            $room = DB::table('rooms')->find($room_id);
            if (!$room) continue;

            $d1 = explode('/', $cart_checkin_date[$i]);
            $d2 = explode('/', $cart_checkout_date[$i]);
            $checkin = strtotime("$d1[2]-$d1[1]-$d1[0]");
            $checkout = strtotime("$d2[2]-$d2[1]-$d2[0]");
            $days = ($checkout - $checkin) / 86400;
            $subtotal = $room->price * $days;

            $detail = new OrderDetail();
            $detail->order_id = $order->id;
            $detail->room_id = $room_id;
            $detail->checkin_date = $cart_checkin_date[$i];
            $detail->checkout_date = $cart_checkout_date[$i];
            $detail->adult = $cart_adult[$i] ?? 1;
            $detail->children = $cart_children[$i] ?? 0;
            $detail->subtotal = $subtotal;
            $detail->save();
        }

        // 🔹 Préparer le mail
        $subject = 'New Order';
        $message = 'You have made an order for hotel booking. Booking details below: <br>';
        $message .= '<br>Order No: ' . $order->order_no;
        $message .= '<br>Transaction Id: ' . $captureId;
        $message .= '<br>Payment Method: PayPal';
        $message .= '<br>Paid Amount: $' . number_format($total_price, 2);
        $message .= '<br>Booking Date: ' . now();
        $message .= '<br><br><strong>Room Details:</strong><br>';

        foreach ($cart_room_id as $i => $room_id) {
            $room = DB::table('rooms')->find($room_id);
            if (!$room) continue;

            $d1 = explode('/', $cart_checkin_date[$i]);
            $d2 = explode('/', $cart_checkout_date[$i]);
            $checkin = strtotime("$d1[2]-$d1[1]-$d1[0]");
            $checkout = strtotime("$d2[2]-$d2[1]-$d2[0]");
            $days = ($checkout - $checkin) / 86400;
            $subtotal = $room->price * $days;

            $message .= '<br><b>Room:</b> ' . e($room->name);
            $message .= '<br>Price per night: $' . number_format($room->price, 2);
            $message .= '<br>Check-in: ' . $cart_checkin_date[$i];
            $message .= '<br>Check-out: ' . $cart_checkout_date[$i];
            $message .= '<br>Nights: ' . $days;
            $message .= '<br>Adults: ' . ($cart_adult[$i] ?? 1);
            $message .= '<br>Children: ' . ($cart_children[$i] ?? 0);
            $message .= '<br>Subtotal: $' . number_format($subtotal, 2) . '<br>';
        }

        // 🔹 Récupérer l'email client depuis le guard pour fiabilité
        // $customer_email = Auth::guard('customer')->user()->email;

        // 🔹 Envoyer mail à l’admin et au client
        try {
            // Admin email (à configurer dans .env ou hardcoder)
            // $admin_email = 'admin@example.com';
            // \Mail::to($admin_email)->send(new Websitemail($subject, $message));

            // Client email
              // Récupérer l'email client depuis le guard
              $customer_email = Auth::guard('customer')->user()->email;

            // Envoi du mail au client
            \Mail::to($customer_email)->send(new Websitemail($subject, $message));

            \Log::info("Mail successfully sent to customer ($customer_email)");
        } catch (\Exception $e) {
            \Log::error('Error sending mail: ' . $e->getMessage());
        }

        // 🔹 Vider le panier
        session()->forget([
            'cart_room_id',
            'cart_checkin_date',
            'cart_checkout_date',
            'cart_adult',
            'cart_children'
        ]);

        // 🔹 Vider les infos de facturation
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

        // 🔹 Redirection vers page succès
        return redirect()->route('payment.success')
            ->with('success', 'Payment successfully confirmed.');

    } catch (\Exception $e) {
        return redirect()->route('payment.cancel')
            ->with('error', 'PayPal error: ' . $e->getMessage());
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


//================ stripe =============================

public function stripeCreateIntent(Request $request)
{
    Stripe::setApiKey(config('services.stripe.secret'));

    $amount = $request->amount * 100; // en cents

    $paymentIntent = PaymentIntent::create([
        'amount' => (int) $amount,
        'currency' => 'usd',
        'automatic_payment_methods' => ['enabled' => true],
    ]);

    return response()->json(['client_secret' => $paymentIntent->client_secret]);
}



public function stripeSuccess(Request $request)
{
    $paymentIntentId = $request->query('payment_intent');
    
    
    if (!$paymentIntentId) {
        return redirect()->route('payment.cancel')->with('error', 'No payment found.');
    }

     // Configure Stripe
    Stripe::setApiKey(config('services.stripe.secret'));

    // Récupère le PaymentIntent depuis Stripe
    $paymentIntent = \Stripe\PaymentIntent::retrieve($paymentIntentId);

      if ($paymentIntent->status !== 'succeeded') {
        return redirect()->route('payment.cancel')->with('error', 'Payment failed.');
      }

    // ✅ Récupération des informations de carte via la charge associée
    $cardLast4 = null;
    $cardBrand = null;

    
    if (isset($paymentIntent->latest_charge)) {
        $latestCharge = \Stripe\Charge::retrieve($paymentIntent->latest_charge);

        if (isset($latestCharge->payment_method_details->card)) {
            $card = $latestCharge->payment_method_details->card;
            $cardLast4 = $card->last4 ?? null;
            $cardBrand = $card->brand ?? null;
        }
    }

    // --- 🔹 Même logique que PayPal ---
    $cart_room_id = session()->get('cart_room_id', []);
    $cart_checkin_date = session()->get('cart_checkin_date', []);
    $cart_checkout_date = session()->get('cart_checkout_date', []);
    $cart_adult = session()->get('cart_adult', []);
    $cart_children = session()->get('cart_children', []);

    $total_price = 0;
    foreach ($cart_room_id as $i => $room_id) {
        $room = DB::table('rooms')->find($room_id);
        if (!$room) continue;

        $d1 = explode('/', $cart_checkin_date[$i]);
        $d2 = explode('/', $cart_checkout_date[$i]);
        $checkin = strtotime("$d1[2]-$d1[1]-$d1[0]");
        $checkout = strtotime("$d2[2]-$d2[1]-$d2[0]");
        $days = ($checkout - $checkin) / 86400;
        $total_price += $room->price * $days;
    }

    // test
    foreach ($cart_room_id as $i => $room_id) {
       if (!$this->isRoomAvailable($room_id, $cart_checkin_date[$i], $cart_checkout_date[$i])) {
        return redirect()->route('cart')->with('error', 'One of the selected rooms is no longer available.');
      }
    }

    $order = new Order();
    $order->customer_id = Auth::guard('customer')->user()->id;
    $order->order_no = strtoupper(uniqid('ORD-'));
    $order->transaction_id = $paymentIntent->id;
    $order->payment_method = 'Stripe';
    $order->card_last_digit = $cardLast4;
    $order->paid_amount = $total_price;
    $order->booking_date = now();
    $order->status = 'completed';
    $order->save();

    foreach ($cart_room_id as $i => $room_id) {
        $room = DB::table('rooms')->find($room_id);
        if (!$room) continue;

        $d1 = explode('/', $cart_checkin_date[$i]);
        $d2 = explode('/', $cart_checkout_date[$i]);
        $checkin = strtotime("$d1[2]-$d1[1]-$d1[0]");
        $checkout = strtotime("$d2[2]-$d2[1]-$d2[0]");
        $days = ($checkout - $checkin) / 86400;
        $subtotal = $room->price * $days;

        $detail = new OrderDetail();
        $detail->order_id = $order->id;
        $detail->room_id = $room_id;
        $detail->checkin_date = $cart_checkin_date[$i];
        $detail->checkout_date = $cart_checkout_date[$i];
        $detail->adult = $cart_adult[$i] ?? 1;
        $detail->children = $cart_children[$i] ?? 0;
        $detail->subtotal = $subtotal;
        $detail->save();
    }

     // --- 🔹 Envoi mail client ---
    $subject = 'Stripe Payment Confirmation';
    $message = 'Your payment with Stripe was successful.<br>Order No: ' . $order->order_no;

    try {
        $customer_email = Auth::guard('customer')->user()->email;
        \Mail::to($customer_email)->send(new \App\Mail\Websitemail($subject, $message));
    } catch (\Exception $e) {
        \Log::error('Stripe mail error: ' . $e->getMessage());
    }

    // --- 🔹 Vider le panier et infos facturation ---
    session()->forget([
        'cart_room_id', 'cart_checkin_date', 'cart_checkout_date',
        'cart_adult', 'cart_children',
        'billing_name', 'billing_email', 'billing_phone',
        'billing_country', 'billing_address', 'billing_state',
        'billing_city', 'billing_zip'
    ]);
   
     return redirect()->route('payment.success')->with('success', 'Stripe payment successful.');



}


// fonction utilitaire 


private function calculateTotal()
{
    $total_price = 0;
    $cart_room_id = session()->get('cart_room_id', []);
    $cart_checkin_date = session()->get('cart_checkin_date', []);
    $cart_checkout_date = session()->get('cart_checkout_date', []);

    foreach ($cart_room_id as $i => $room_id) {
        $room = DB::table('rooms')->where('id', $room_id)->first();
        if (!$room) continue;

        $d1 = explode('/', $cart_checkin_date[$i]);
        $d2 = explode('/', $cart_checkout_date[$i]);
        $checkin = strtotime($d1[2] . '-' . $d1[1] . '-' . $d1[0]);
        $checkout = strtotime($d2[2] . '-' . $d2[1] . '-' . $d2[0]);
        $nights = ($checkout - $checkin) / 60 / 60 / 24;
        $total_price += $room->price * $nights;
    }

    return $total_price;
}

// test
private function isRoomAvailable($room_id, $checkin_date, $checkout_date)
{
    // ⚠️ Convertir les dates en format Y-m-d
    $d1 = explode('/', $checkin_date);
    $d2 = explode('/', $checkout_date);
    $checkin = date('Y-m-d', strtotime("$d1[2]-$d1[1]-$d1[0]"));
    $checkout = date('Y-m-d', strtotime("$d2[2]-$d2[1]-$d2[0]"));

    // Vérifier s’il existe un chevauchement avec une autre réservation
    $exists = DB::table('order_details')
        ->join('orders', 'order_details.order_id', '=', 'orders.id')
        ->where('order_details.room_id', $room_id)
        ->where('orders.status', 'completed') // uniquement les réservations confirmées
        ->where(function ($q) use ($checkin, $checkout) {
            $q->whereBetween('order_details.checkin_date', [$checkin, $checkout])
              ->orWhereBetween('order_details.checkout_date', [$checkin, $checkout])
              ->orWhere(function ($q2) use ($checkin, $checkout) {
                  $q2->where('order_details.checkin_date', '<=', $checkin)
                     ->where('order_details.checkout_date', '>=', $checkout);
              });
        })
        ->exists();

    return !$exists; // true si libre, false si déjà réservé
}




}
