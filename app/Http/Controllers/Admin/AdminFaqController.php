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
}
