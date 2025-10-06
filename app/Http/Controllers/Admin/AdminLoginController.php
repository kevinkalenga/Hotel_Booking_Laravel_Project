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

    public function forget_password_submit(Request $request)
    {
        $request->validate([
            'email' => 'required|email'
        ]);

        
        $admin_data = Admin::where('email',$request->email)->first();
        if(!$admin_data) {
          return redirect()->back()->with('error','Email is not found');
        }

           $token = hash('sha256',time());
           $admin_data->token = $token;
           $admin_data->update();
           $reset_link = url('admin/reset-password/'.$token.'/'.$request->email);
           $subject = "Password Reset";
           $message = "To reset password, please click on the link below:<br>";
           $message .= "<a href='".$reset_link."'>Click Here</a>";

           \Mail::to($request->email)->send(new Websitemail($subject,$message));

            // return redirect()->back()->with('success','We have sent a password reset link to your email. Please check your email. If you do not find the email in your inbox, please check your spam folder.');
            return redirect()->route('admin_login')->with('success','Please check your email and follow the steps there.');
    }
    
    
    
    
    
    
    
    
    
    public function login_submit(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required'
        ]);
        
        // if everything is okey, i get my fields
        $credential = [
            'email' => $request->email,
            'password' => $request->password
        ];

        // i use my auth guard which is admin
        if(Auth::guard('admin')->attempt($credential)) {
            return redirect()->route('admin_home');
        } else {
            return redirect()->route('admin_login')->with('error', 'Information is not correct!');
        }
    }

    public function logout()
    {
        Auth::guard('admin')->logout();
        return redirect()->route('admin_login');
    }
}
