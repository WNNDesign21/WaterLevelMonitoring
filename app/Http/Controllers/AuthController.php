<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Laravel\Socialite\Facades\Socialite;
use App\Models\ActivityLog;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Carbon\Carbon;
use App\Mail\PasswordResetMail;
use App\Services\WhatsAppService;

class AuthController extends Controller
{
    // ... existing methods ...

    /**
     * Google SSO Redirect
     */
    public function redirectToGoogle()
    {
        return Socialite::driver('google')->redirect();
    }

    /**
     * Google SSO Callback
     */
    public function handleGoogleCallback()
    {
        try {
            $googleUser = Socialite::driver('google')->user();
            
            $user = User::where('google_id', $googleUser->id)
                ->orWhere('email', $googleUser->email)
                ->first();

            if (!$user) {
                // Create new user shell
                $user = User::create([
                    'name' => $googleUser->name,
                    'email' => $googleUser->email,
                    'google_id' => $googleUser->id,
                    'password' => null, // Password can be null for Google users
                    'avatar' => $googleUser->avatar,
                    'role' => 'Warga',
                ]);
            } else if (!$user->google_id) {
                // Link Google account to existing email
                $user->update(['google_id' => $googleUser->id]);
            }

            Auth::login($user);

            ActivityLog::create([
                'user_id' => $user->id,
                'event_type' => 'login_sso_google',
                'description' => 'User logged in via Google SSO',
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
            ]);

            // If must change password, redirect to change password page
            if ($user->must_change_password) {
                return redirect()->route('password.change.show');
            }

            // If profile is incomplete, redirect to completion page
            if (!$user->phone || !$user->latitude) {
                return redirect()->route('register.complete');
            }

            // Redirect based on role
            if ($user->role === 'Administrator IT') {
                return redirect()->route('it.dashboard');
            }

            return redirect()->intended(route('user.dashboard'));

        } catch (\Exception $e) {
            return redirect()->route('login')->withErrors(['email' => 'Gagal masuk menggunakan Google.']);
        }
    }

    public function showCompleteProfile()
    {
        return view('auth.complete_profile');
    }

    public function completeProfile(Request $request)
    {
        $request->validate([
            'phone' => 'required|string',
            'address' => 'required|string',
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
            'emergency_phone' => 'required|string',
        ]);

        $user = Auth::user();
        $user->update([
            'phone' => $request->phone,
            'address' => $request->address,
            'latitude' => $request->latitude,
            'longitude' => $request->longitude,
            'emergency_phone' => $request->emergency_phone,
        ]);

        return redirect()->route('user.dashboard');
    }

    public function showChangePassword()
    {
        return view('auth.change_password');
    }

    public function changePassword(Request $request)
    {
        $request->validate([
            'password' => 'required|string|min:8|confirmed',
        ]);

        $user = Auth::user();
        $user->update([
            'password' => Hash::make($request->password),
            'must_change_password' => false,
        ]);

        return redirect()->route('user.dashboard')->with('success', 'Password berhasil diperbarui. Selamat datang di WaterSense!');
    }

    public function showLogin()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user) {
            ActivityLog::create([
                'event_type' => 'login_failed_email',
                'description' => 'Failed login attempt: Email not found (' . $request->email . ')',
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
            ]);
            return back()->withErrors([
                'email' => 'Email tidak ditemukan dalam database kami.',
            ])->withInput($request->only('email'));
        }

        if (!Hash::check($request->password, $user->password)) {
            ActivityLog::create([
                'user_id' => $user->id,
                'event_type' => 'login_failed_password',
                'description' => 'Failed login attempt: Incorrect password',
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
            ]);
            return back()->withErrors([
                'password' => 'Password yang Anda masukkan salah.',
            ])->withInput($request->only('email'));
        }

        Auth::login($user, $request->has('remember'));

        ActivityLog::create([
            'user_id' => $user->id,
            'event_type' => 'login_success',
            'description' => 'User logged in successfully',
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);
        
        $request->session()->regenerate();

        // If must change password, redirect to change password page
        if ($user->must_change_password) {
            return redirect()->route('password.change.show');
        }

        // Check if profile is complete
        if (!$user->phone || !$user->latitude) {
            return redirect()->route('register.complete');
        }

        // Redirect based on role
        if ($user->role === 'Administrator IT') {
            return redirect()->route('it.dashboard');
        }

        return redirect()->intended(route('user.dashboard'));
    }

    public function showRegister()
    {
        return view('auth.register');
    }

    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'phone' => 'required|string',
            'address' => 'required|string',
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
            'emergency_phone' => 'required|string',
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'phone' => $request->phone,
            'address' => $request->address,
            'latitude' => $request->latitude,
            'longitude' => $request->longitude,
            'emergency_phone' => $request->emergency_phone,
            'role' => 'Warga',
        ]);

        Auth::login($user);

        return redirect()->route('user.dashboard');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/');
    }

    /**
     * Password Reset Methods
     */
    public function showForgotPasswordForm()
    {
        return view('auth.forgot-password');
    }

    public function sendResetLinkEmail(Request $request)
    {
        $request->validate(['email' => 'required|email|exists:users,email']);

        $token = Str::random(64);

        DB::table('password_reset_tokens')->updateOrInsert(
            ['email' => $request->email],
            [
                'token' => $token,
                'created_at' => Carbon::now()
            ]
        );

        Mail::to($request->email)->send(new PasswordResetMail($token, $request->email));

        return back()->with('status', 'Kami telah mengirimkan link reset password ke email Anda!');
    }

    public function showResetPasswordForm($token, Request $request)
    {
        return view('auth.reset-password', ['token' => $token, 'email' => $request->email]);
    }

    public function resetPassword(Request $request)
    {
        $request->validate([
            'token' => 'required',
            'email' => 'required|email|exists:users,email',
            'password' => 'required|string|min:8|confirmed',
        ]);

        $resetData = DB::table('password_reset_tokens')
            ->where('email', $request->email)
            ->where('token', $request->token)
            ->first();

        if (!$resetData) {
            return back()->withErrors(['email' => 'Token reset password tidak valid atau sudah kadaluwarsa.']);
        }

        // Check if token is expired (e.g., 60 minutes)
        if (Carbon::parse($resetData->created_at)->addMinutes(60)->isPast()) {
            return back()->withErrors(['email' => 'Token reset password sudah kadaluwarsa.']);
        }

        $user = User::where('email', $request->email)->first();
        $user->update([
            'password' => Hash::make($request->password),
            'must_change_password' => false // Ensure they can log in normally now
        ]);

        DB::table('password_reset_tokens')->where(['email' => $request->email])->delete();

        return redirect()->route('login')->with('success', 'Password berhasil direset. Silakan login dengan password baru Anda.');
    }

    public function sendResetLinkViaWhatsApp(Request $request, WhatsAppService $waService)
    {
        $request->validate(['email' => 'required|email|exists:users,email']);

        $user = User::where('email', $request->email)->first();
        
        if (!$user->phone) {
            return back()->withErrors(['email' => 'Nomor WhatsApp tidak terdaftar di akun ini. Silakan hubungi Admin IT.']);
        }

        $token = Str::random(64);

        DB::table('password_reset_tokens')->updateOrInsert(
            ['email' => $request->email],
            [
                'token' => $token,
                'created_at' => Carbon::now()
            ]
        );

        $resetUrl = url('password/reset/'.$token.'?email='.$request->email);
        
        $message = "🛡️ *WaterSense Security Protocol*\n\n";
        $message .= "Halo *" . $user->name . "*,\n\n";
        $message .= "Kami menerima permintaan reset password untuk akun Anda. Silakan klik link di bawah ini untuk mengatur ulang password Anda:\n\n";
        $message .= $resetUrl . "\n\n";
        $message .= "⚠️ *Link ini hanya berlaku selama 60 menit.*\n";
        $message .= "Jika Anda tidak merasa melakukan permintaan ini, abaikan pesan ini.";

        $sent = $waService->sendMessage($user->phone, $message);

        if ($sent) {
            return back()->with('status', 'Link reset password telah dikirim ke WhatsApp Anda (' . $user->phone . ')!');
        }

        return back()->withErrors(['email' => 'Gagal mengirim pesan WhatsApp. Pastikan layanan Fonnte aktif atau hubungi Admin.']);
    }
}
