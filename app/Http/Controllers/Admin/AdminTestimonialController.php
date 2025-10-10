<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Testimonial;

class AdminTestimonialController extends Controller
{
    public function index()
    {
        $testimonials = Testimonial::get();
        return view('admin.testimonial_view', compact('testimonials'));
    }


    public function add()
    {
        return view('admin.testimonial_add');
    }
    public function store(Request $request)
    {
            $request->validate([
            'photo' => ['image', 'mimes:jpg,jpeg,png,gif', 'max:2048'],
             'name' => 'required',
            'designation' => 'required',
            'comment' => 'required',
        ]);

         $final_name = null;

       

             if ($request->hasFile('photo')) {
                $ext = $request->file('photo')->extension();
                $finale_name = time().'.'.$ext;
                $request->file('photo')->move(public_path('uploads/'), $finale_name);

                $obj = new Testimonial();
                $obj->photo = $finale_name;
                $obj->name = $request->name;
                $obj->designation = $request->designation;
                $obj->comment = $request->comment;
              
                $obj->save();
            } else {
                return back()->withErrors(['photo' => 'No file uploaded']);
            }

        return redirect()->back()->with('success', 'Testimonial is added Successfully');
    }

}
