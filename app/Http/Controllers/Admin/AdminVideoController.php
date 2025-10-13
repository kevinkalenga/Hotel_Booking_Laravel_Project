<?php

namespace App\Http\Controllers\Admin;
use App\Models\Video;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class AdminVideoController extends Controller
{
    public function index()
    {
        $videos = Video::get();
        return view('admin.video_view', compact('videos'));
    }
}
