<?php

namespace App\Http\Controllers\Front;
use App\Models\Page;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ContactController extends Controller
{
    public function index()
    {
          $contact_data = Page::where('id', 1)->first(); 
          return view('front.contact', compact('contact_data'));

       
    }
}
