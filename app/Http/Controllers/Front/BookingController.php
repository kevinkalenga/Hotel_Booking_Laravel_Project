<?php

namespace App\Http\Controllers\Front;
use App\Models\Customer;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Auth;

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

}
