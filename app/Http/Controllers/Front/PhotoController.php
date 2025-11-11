<?php

namespace App\Http\Controllers\Front;
use App\Models\Photo;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class PhotoController extends Controller
{
    public function index()
    {
          $photo_all = Photo::paginate(4); //Limiter le nombre de postes
          return view('front.photo_gallery', compact('photo_all'));
    }
}
