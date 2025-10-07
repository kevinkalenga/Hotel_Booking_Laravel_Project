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
}
