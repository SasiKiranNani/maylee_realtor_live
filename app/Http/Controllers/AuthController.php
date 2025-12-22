<?php

namespace App\Http\Controllers;

use Devrabiul\ToastMagic\Facades\ToastMagic;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use App\Models\User;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    /**
     * Handle user registration.
     */
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'userFullName' => 'required|string|max:255',
            'userEmail' => 'required|email|unique:users,email',
            'userPassword' => 'required|min:6|confirmed',
        ], [
            'userPassword.confirmed' => 'Passwords do not match.'
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        $user = User::create([
            'name' => $request->userFullName,
            'email' => $request->userEmail,
            'password' => Hash::make($request->userPassword),
        ]);

        // Assign default role
        $user->assignRole('user');

        Auth::login($user);

        // Redirect to home after successful registration
        return redirect()->route('home');
    }

    /**
     * Handle user login.
     */
    public function login(Request $request)
    {
        $request->validate([
            'userName' => 'required',
            'userPwd' => 'required',
        ]);

        $loginField = filter_var($request->userName, FILTER_VALIDATE_EMAIL) ? 'email' : 'name';
        $user = User::where($loginField, $request->userName)->first();

        if (!$user) {
            ToastMagic::error('Invalid credentials or this account is not registered.');
            return back()->withInput();
        }

        if (Auth::attempt([$loginField => $request->userName, 'password' => $request->userPwd], $request->remember)) {
            $request->session()->regenerate();
            ToastMagic::success('Login successful!');
            return redirect()->back();
        }

        ToastMagic::error('Wrong password.');
        return back()->withInput();
    }

    /**
     * Handle logout.
     */
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->back(); // ✅ Redirect to home
    }

    /**
     * Send reset password link.
     */
    public function sendResetLink(Request $request)
    {
        $request->validate([
            'userEmail' => 'required|email'
        ]);

        $status = Password::sendResetLink([
            'email' => $request->userEmail
        ]);

        return $status === Password::RESET_LINK_SENT
            ? back()->with('success', 'Reset link sent to your email.')
            : back()->withErrors(['userEmail' => __($status)]);
    }

    /**
     * Show reset password form (when user clicks the email link).
     */
    public function showResetForm($token)
    {
        return view('frontend.reset-password', ['token' => $token]);
    }

    /**
     * Handle actual password reset.
     */
    public function resetPassword(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|min:6|confirmed',
            'token' => 'required'
        ]);

        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function ($user, $password) {
                $user->forceFill([
                    'password' => Hash::make($password),
                    'remember_token' => Str::random(60),
                ])->save();
            }
        );

        if ($status == Password::PASSWORD_RESET) {
            return redirect()->route('home')->with('success', 'Password reset successful.');
        }

        return back()->withErrors(['email' => [__($status)]]);
    }
}
