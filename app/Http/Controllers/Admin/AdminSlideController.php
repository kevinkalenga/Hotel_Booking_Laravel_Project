<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Slider;

class AdminSlideController extends Controller
{
    public function index()
    {
        $slides = Slider::get();
        return view('admin.slide_view', compact('slides'));
    }

    public function add()
    {
        return view('admin.slide_add');
    }
    public function store(Request $request)
    {
            $request->validate([
            'photo' => ['image', 'mimes:jpg,jpeg,png,gif', 'max:2048'],
        ]);

        // $finale_name = 'slider_'.time().'.'.$request->photo->extension();
        // $request->photo->move(public_path('uploads'), $finale_name);

             if ($request->hasFile('photo')) {
                $ext = $request->file('photo')->extension();
                $finale_name = time().'.'.$ext;
                $request->file('photo')->move(public_path('uploads/'), $finale_name);

                $obj = new Slider();
                $obj->photo = $finale_name;
                $obj->heading = $request->heading;
                $obj->text = $request->text;
                $obj->button_text = $request->button_text;
                $obj->button_url = $request->button_url;
                $obj->save();
            } else {
                return back()->withErrors(['photo' => 'No file uploaded']);
            }

        return redirect()->back()->with('success', 'Slider is added Successfully');
    }
}

