<?php

namespace App\Http\Controllers\Admin;
use App\Models\Faq;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class AdminFaqController extends Controller
{
    public function index()
    {
        $faqs = Faq::get();
        return view('admin.faq_view', compact('faqs'));
    }

    public function add()
    {
        return view('admin.faq_add');
    }

    public function store(Request $request)
    {
            $request->validate([
            'question' => 'required',
            'answer' => 'required'
             
        ]);

        $obj = new Faq();
        $obj->question = $request->question;
        $obj->answer = $request->answer;
        $obj->save();

        return redirect()->back()->with('success', 'FAQ is added Successfully');
    }

    public function edit($id)
    {
        // check all the items from the slide
        $faq_data = Faq::where('id', $id)->first();

        return view('admin.faq_edit', compact('faq_data'));
    }


    public function update(Request $request, $id)
    {
       $obj = Faq::where('id', $id)->first();

        $obj->question = $request->question;
        $obj->answer = $request->answer;
        $obj->save();

        return redirect()->back()->with('success', 'FAQ updated successfully');
    }

    public function delete($id)
    {
        $single_data = Faq::where('id', $id)->first();
        $single_data->delete();

         return redirect()->back()->with('success', 'Faq deleted successfully');
    }
}
