<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

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

    // public function cart_delete($id)
    // {
      
    //      $arr_cart_room_id = array();
    //      $i=0;
    //      foreach(session()->get('cart_room_id') as $value){
    //        $arr_cart_room_id[$i] = $value;
    //        $i++;
    //      }
                                     
    //      $arr_cart_checkin_date = array();
    //      $i=0;
    //      foreach(session()->get('cart_checkin_date') as $value){
    //        $arr_cart_checkin_date[$i] = $value;
    //        $i++;
    //      }
                                     
    //      $arr_cart_checkout_date = array();
    //      $i=0;
    //      foreach(session()->get('cart_checkout_date') as $value){
    //        $arr_cart_checkout_date[$i] = $value;
    //        $i++;
    //      }
                                     
    //      $arr_cart_adult = array();
    //      $i=0;
    //      foreach(session()->get('cart_adult') as $value){
    //        $arr_cart_adult[$i] = $value;
    //        $i++;
    //      }
    //      $arr_cart_children = array();
    //      $i=0;
    //      foreach(session()->get('cart_children') as $value){
    //        $arr_cart_children[$i] = $value;
    //        $i++;
    //      }

    //     session()->forget('cart_room_id');
    //     session()->forget('cart_checkin_date');
    //     session()->forget('cart_checkout_date');
    //     session()->forget('cart_adult');
    //     session()->forget('cart_children');

    //     for($i=0; $i < count($arr_cart_room_id); $i++) {
    //        if($arr_cart_room_id[$i] == $id) {

    //        } else {
    //               session()->push('cart_room_id', $arr_cart_room_id[$i]);
    //               session()->push('cart_checkin_date', $arr_cart_checkin_date[$i]);
    //               session()->push('cart_checkout_date', $arr_cart_checkout_date[$i]);
    //               session()->push('cart_adult', $arr_cart_adult[$i]);
    //               session()->push('cart_children', $arr_cart_children[$i]);
    //        }
    //     }

    //      return redirect()->back()->with('success', 'Cart item is deleted Successfully');
    // }

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

}
