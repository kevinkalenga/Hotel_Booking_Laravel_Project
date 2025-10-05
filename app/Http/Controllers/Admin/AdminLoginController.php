<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Mail\Websitemail;
use Auth;
use Hash;
use App\Models\Admin;

class AdminLoginController extends Controller
{
    
    public function index()
    {
        // $pass = Hash::make('12345678');
        // dd($pass);
        return view('admin.login');
    }
    public function forget_password()
    {
        return view('admin.forget_password');
    }
}
