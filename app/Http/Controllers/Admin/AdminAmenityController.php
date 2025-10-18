<?php

namespace App\Http\Controllers\Admin;
use App\Models\Amenity;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class AdminAmenityController extends Controller
{
    public function index()
    {
        $amenities = Amenity::get();
        return view('admin.amenity_view', compact('amenities'));
    }
}
