<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Room;

class RoomController extends Controller
{
    // public function index()
    // {
    //       $post_all = Post::paginate(9); //Limiter le nombre de postes
    //       return view('front.blog', compact('post_all'));
    // }
    public function single_room($id)
    {
        // rRoomPhoto is the relation
        $single_room_data = Room::with('rRoomPhoto')->where('id', $id)->first();
        
        return view('front.room_detail', compact('single_room_data'));
    }
}
