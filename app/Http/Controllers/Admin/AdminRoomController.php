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
            'name' => 'required',
            'description' => 'required',
            'price' => 'required',
            'total_rooms' => 'required',
          
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



    public function edit($id)
    {
        // check all the items from the slide
        $all_amenities = Amenity::get();
        $room_data = Room::where('id', $id)->first();

        $existing_amenities = array();
        if($room_data->amenities != '') {
            $existing_amenities = explode(',', $room_data->amenities);
            
        }

        return view('admin.room_edit', compact('room_data', 'all_amenities', 'existing_amenities'));
    }

    
    // public function update(Request $request, $id)
    // {
        
        
    //     //   $amenities = '';
    //     //    $i=0;
    //     //    if(isset($request->arr_amenities)) {
    //     //       foreach($request->arr_amenities as $item) {
    //     //         if($i == 0) {
    //     //             $amenities .= $item;
    //     //         } else {
    //     //             $amenities .= ','.$item;
    //     //         }
    //     //         $i++;
    //     //       }
    //     //    }
        
        
        
    //     $request->validate([
    //         'featured_photo' => ['nullable', 'image', 'mimes:jpg,jpeg,png,gif', 'max:2048'],
    //         'name' => 'required',
    //         'description' => 'required',
    //         'price' => 'required',
    //         'total_rooms' => 'required',
           
    //     ]);

    //     $obj = Room::findOrFail($id);

    //     if ($request->hasFile('photo')) {
    //         // delete old photo if exists
    //         if ($obj->photo && file_exists(public_path('uploads/' . $obj->photo))) {
    //             unlink(public_path('uploads/' . $obj->photo));
    //         }

    //         $ext = $request->file('photo')->extension();
    //         $final_name = time() . '.' . $ext;
    //         $request->file('photo')->move(public_path('uploads/'), $final_name);

    //         $obj->photo = $final_name;
    //     }

            
    //     $obj->featured_photo = $finale_name;
    //     $obj->name = $request->name;
    //     $obj->description = $request->description;
    //     $obj->price = $request->price;
    //     $obj->total_rooms = $request->total_rooms;
    //     $obj->save();

    //     return redirect()->back()->with('success', 'Slider updated successfully');
    // }



    public function update(Request $request, $id)
{
    // 1️⃣ Validation
    $request->validate([
        'featured_photo' => ['nullable', 'image', 'mimes:jpg,jpeg,png,gif', 'max:2048'],
        'name' => 'required',
        'description' => 'required',
        'price' => 'required',
        'total_rooms' => 'required',
    ]);

    // 2️⃣ Récupération de la room existante
    $obj = Room::findOrFail($id);

    // 3️⃣ Gestion de la photo (si remplacée)
    if ($request->hasFile('featured_photo')) {
        // Supprimer l’ancienne si elle existe
        if ($obj->featured_photo && file_exists(public_path('uploads/' . $obj->featured_photo))) {
            unlink(public_path('uploads/' . $obj->featured_photo));
        }

        $ext = $request->file('featured_photo')->extension();
        $final_name = time() . '.' . $ext;
        $request->file('featured_photo')->move(public_path('uploads/'), $final_name);

        $obj->featured_photo = $final_name;
    }

    // 4️⃣ Gérer les amenities
    $amenities = '';
    if ($request->has('arr_amenities')) {
        $amenities = implode(',', $request->arr_amenities);
    }

    // 5️⃣ Mettre à jour les autres champs
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

    // 6️⃣ Sauvegarde
    $obj->save();

    return redirect()->back()->with('success', 'Room updated successfully');
}

    
    public function delete($id)
    {
        $single_data = Slider::where('id', $id)->first();
        unlink(public_path('uploads/'.$single_data->photo));
        $single_data->delete();

         return redirect()->back()->with('success', 'Slider deleted successfully');
    }
}
