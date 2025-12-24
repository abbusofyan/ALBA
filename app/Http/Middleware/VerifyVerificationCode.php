<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class VerifyVerificationCode
{
    public function handle(Request $request, Closure $next)
    {
        if (!Auth::check()) {
            return redirect()->route('logout.get');
        }

        if (!session()->has('code_verified')) {
            return redirect()->route('verify.code');
        }

        if (Auth::user()->session_id !== Session::getId()) {
            return redirect()->route('logout.get');
        }

        return $next($request);
    }
}
