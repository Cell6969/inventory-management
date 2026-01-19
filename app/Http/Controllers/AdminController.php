<?php

namespace App\Http\Controllers;

use App\Mail\VerificationCodeMail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use App\Models\User;
use Illuminate\Support\Facades\Mail;

class AdminController extends Controller
{
    public function destroy(Request $request)
    {

        $user = Auth::user();
        Log::info($user);

        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/login');
    }

    public function login(Request $request)
    {
        $credentials = $request->only('email', 'password');

        if (Auth::attempt($credentials)) {
            $user = Auth::user();
            $verification_code = random_int(100000, 999999);
            session(['verification_code' => $verification_code, 'user_id' => $user->id]);

            Mail::to($user->email)->send(new VerificationCodeMail($verification_code));
            Auth::logout();
            return redirect()->route('custom.verification.form')->with('status', 'Verification code has been sent to your email.');
        }

        return redirect()->back()->withErrors(['email' => 'Invalid Credentials for email and password.']);
    }

    public function showVerification(Request $request)
    {
        return view('auth.verify-code');
    }

    public function submitVerification(Request $request)
    {
        $request->validate(['code' => 'required | numeric']);
        if ($request->code == session('verification_code')) {
            Auth::loginUsingId(session('user_id'));
            session()->forget(['user_id', 'verification_code']);
            return redirect()->intended('/dashboard');
        }

        return redirect()->back()->withErrors(['code' => 'Invalid verification code.']);
    }
}
