<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class AuthController extends Controller
{
    protected $blockedDomains = ['ganteng.com'];

    public function showLoginForm()
    {
        return view('auth.login');
    }

    public function showRegisterForm()
    {
        return view('auth.register');
    }

    public function showForgotPasswordForm()
    {
        return view('auth.forgot-password');
    }

    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
        ]);

        if ($validator->fails()) {
            if ($request->expectsJson()) {
                return response()->json(['errors' => $validator->errors()], 422);
            }
            return back()->withErrors($validator)->withInput();
        }

        // Check blocked domains
        $email = $request->email;
        $domain = substr(strrchr($email, "@"), 1);
        if (in_array(strtolower($domain), $this->blockedDomains)) {
            $error = ['email' => ['username/password incorrect']];
            if ($request->expectsJson()) {
                return response()->json(['errors' => $error], 422);
            }
            return back()->withErrors($error)->withInput();
        }

        $user = User::create([
            'email' => $request->email,
            'hashed_password' => Hash::make($request->password),
        ]);

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Registrasi berhasil'
            ]);
        }

        return redirect()->route('login')->with('success', 'Registrasi berhasil');
    }

    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required|string|min:8',
        ]);

        if ($validator->fails()) {
            if ($request->expectsJson()) {
                return response()->json(['error' => 'username/password incorrect'], 401);
            }
            return back()->withErrors(['email' => 'username/password incorrect'])->withInput();
        }

        // Check blocked domains
        $email = $request->email;
        $domain = substr(strrchr($email, "@"), 1);
        if (in_array(strtolower($domain), $this->blockedDomains)) {
            if ($request->expectsJson()) {
                return response()->json(['error' => 'username/password incorrect'], 401);
            }
            return back()->withErrors(['email' => 'username/password incorrect'])->withInput();
        }

        // Find user and verify password
        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->hashed_password)) {
            if ($request->expectsJson()) {
                return response()->json(['error' => 'username/password incorrect'], 401);
            }
            return back()->withErrors(['email' => 'username/password incorrect'])->withInput();
        }

        // Create Passport token
        $token = $user->createToken('auth_token')->accessToken;

        if ($request->expectsJson()) {
            return response()->json([
                'access_token' => $token,
                'token_type' => 'Bearer',
                'expires_in' => 30 * 60 // 30 minutes in seconds
            ]);
        }

        return redirect()->route('dashboard')->with('token', $token);
    }

    public function sendResetEmail(Request $request)
    {
        $email = $request->email;

        if (empty($email)) {
            if ($request->expectsJson()) {
                return response()->json(['message' => 'Email tidak boleh kosong'], 422);
            }
            return back()->withErrors(['email' => 'Email tidak boleh kosong']);
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            if ($request->expectsJson()) {
                return response()->json(['message' => 'Email Anda Salah'], 422);
            }
            return back()->withErrors(['email' => 'Email Anda Salah']);
        }

        $user = User::where('email', $email)->first();
        if (!$user) {
            if ($request->expectsJson()) {
                return response()->json(['message' => 'Email Anda Salah'], 422);
            }
            return back()->withErrors(['email' => 'Email Anda Salah']);
        }

        // Generate OTP and temp password
        $otp = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);
        $tempPassword = Str::random(10);

        // Update user with reset token and new password
        $user->reset_token = $otp;
        $user->hashed_password = Hash::make($tempPassword);
        $user->save();

        // Send email
        try {
            \Log::info('Sending reset password email to: ' . $email);

            Mail::raw("Halo,\n\nBerikut adalah informasi reset password Anda:\n\nOTP: {$otp}\nPassword Baru: {$tempPassword}\n\nSilakan login dengan password baru dan segera ubah password Anda.\n\nSalam,\nSleepy Panda", function ($message) use ($email) {
                $message->to($email)
                    ->subject('Reset Password - Sleepy Panda');
            });

            \Log::info('Email sent successfully to: ' . $email);
        } catch (\Exception $e) {
            \Log::error('Failed to send email: ' . $e->getMessage());

            if ($request->expectsJson()) {
                return response()->json(['message' => 'Gagal mengirim email: ' . $e->getMessage()], 500);
            }
            return back()->withErrors(['email' => 'Gagal mengirim email. Silakan coba lagi.']);
        }

        if ($request->expectsJson()) {
            return response()->json(['message' => 'OTP dan password baru telah dikirim ke email Anda.']);
        }

        return back()->with('status', 'OTP dan password baru telah dikirim ke email Anda.');
    }

    public function logout(Request $request)
    {
        $request->user()->token()->revoke();

        if ($request->expectsJson()) {
            return response()->json(['message' => 'Successfully logged out']);
        }

        return redirect()->route('login');
    }

    public function me(Request $request)
    {
        return response()->json($request->user());
    }
}
