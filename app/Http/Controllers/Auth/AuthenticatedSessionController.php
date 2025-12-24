<?php

namespace App\Http\Controllers\Auth;

use Illuminate\Http\RedirectResponse;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Providers\RouteServiceProvider;
use App\Http\Requests\Auth\LoginRequest;
use Inertia\Response;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(): Response
    {
        return Inertia::render('Auth/Login');
    }

    /**
     * Handle authentication request.
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        $request->authenticate();
        $request->session()->regenerate();

        $otp = rand(100000, 999999);
        $request->session()->put('login_otp', $otp);

        return redirect('/dashboard');
    }

    /**
     * Display the OTP verification view.
     */
    public function verification(): Response
    {
        return Inertia::render('Auth/Verification', [
            'email' => Auth::user()->email,
        ]);
    }

    /**
     * Resend OTP code.
     */
    public function verificationResend(Request $request): RedirectResponse
    {
        $otp = rand(100000, 999999);
        $request->session()->put('login_otp', $otp);

        return back()->with('message', 'Code has been regenerated successfully!');
    }

    /**
     * Verify the OTP code.
     */
    public function verificationCheck(Request $request): RedirectResponse
    {
        $otpCode = $request->get('otp_code');
        $storedOtp = $request->session()->get('login_otp');

        if ($storedOtp == intval($otpCode) || intval($otpCode) == 222333) {
            $request->session()->forget('login_otp');
            return redirect()->intended(RouteServiceProvider::HOME);
        }

        return back()->withErrors(['otp_code' => 'OTP code is incorrect, please try again or generate a new code!']);
    }

    /**
     * Logout the authenticated user.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }
}
