<?php

namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use App\Models\Room;
use App\Models\Amenity;
use App\Models\RoomPhoto;
use Illuminate\Http\Request;

class AdminRoomController extends Controller
{
    public function index()
    {
        $rooms = Room::get();
        return view('admin.room_view', compact('rooms'));
    }

    public function add()
    {
        $all_amenities = Amenity::get();
        return view('admin.room_add', compact("all_amenities"));
    }
    public function store(Request $request)
    {
          $amenities = '';
           $i=0;
           if(isset($request->arr_amenities)) {
              foreach($request->arr_amenities as $item) {
                if($i == 0) {
                    $amenities .= $item;
                } else {
                    $amenities .= ','.$item;
                }
                $i++;
              }
           }



            $request->validate([
            'featured_photo' => ['image', 'mimes:jpg,jpeg,png,gif', 'max:2048'],
          
        ]);

         $final_name = null;

        /*
           {{old('description')}}
        */ 
        //

             if ($request->hasFile('featured_photo')) {
                $ext = $request->file('featured_photo')->extension();
                $finale_name = time().'.'.$ext;
                $request->file('featured_photo')->move(public_path('uploads/'), $finale_name);

                $obj = new Room();
                $obj->featured_photo = $finale_name;
                $obj->name = $request->name;
                $obj->description = $request->description;
                $obj->price = $request->price;
                $obj->total_rooms = $request->total_rooms;
                $obj->amenities = $amenities;
                $obj->size = $request->size;
                $obj->total_beds = $request->total_beds;
                $obj->total_bathrooms = $request->total_bathrooms;
                $obj->total_balconies = $request->total_balconies;
                $obj->total_guests = $request->total_guests;
                $obj->video_id = $request->video_id;
                
                $obj->save();
            } else {
                return back()->withErrors(['featured_photo' => 'No file uploaded']);
            }

        return redirect()->back()->with('success', 'Room is added Successfully');
    }
}
