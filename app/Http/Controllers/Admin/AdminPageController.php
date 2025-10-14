<?php

namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use App\Models\Page;
use Illuminate\Http\Request;

class AdminPageController extends Controller
{
    public function about()
    {
        $page_data = Page::where('id', 1)->first();
        return view('admin.page_about', compact('page_data'));
    }

    public function about_update(Request $request)
    {
        $obj = Page::where('id', 1)->first();

        $obj->about_heading = $request->about_heading;
        $obj->about_content = $request->about_content;
        $obj->save();

        return redirect()->back()->with('success', 'Data is updated successfully');
    }
}
