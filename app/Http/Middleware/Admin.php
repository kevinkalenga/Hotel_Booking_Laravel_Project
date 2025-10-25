<?php

namespace App\Http\Middleware;

use Illuminate\Auth\Middleware\Authenticate as Middleware;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class Admin 
{
   public function handle(Request $request, Closure $next)
    {
        // Vérifie si l'admin est connecté avec le bon guard
        if (!Auth::guard('admin')->check()) {
            return redirect()->route('admin_login');
        }

        return $next($request);
    }
} 
