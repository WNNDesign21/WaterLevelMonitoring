<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Carbon\Carbon;
use App\Mail\PasswordResetMail;
use App\Services\WhatsAppService;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $user = User::where('email', $request->email)->first();

        // Check if user exists
        if (!$user) {
            ActivityLog::create([
                'user_id' => null,
                'event_type' => 'login_failed_api',
                'description' => 'Login attempt for non-existent email: ' . $request->email,
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
            ]);

            return response()->json([
                'message' => 'Akun dengan email ini belum terdaftar.'
            ], 404);
        }

        // Check if password matches
        if (!Hash::check($request->password, $user->password)) {
            ActivityLog::create([
                'user_id' => $user->id,
                'event_type' => 'login_failed_api',
                'description' => 'Incorrect password attempt for ' . $request->email,
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
            ]);

            return response()->json([
                'message' => 'Password yang Anda masukkan salah.'
            ], 401);
        }

        $token = $user->createToken('mobile_app')->plainTextToken;

        ActivityLog::create([
            'user_id' => $user->id,
            'event_type' => 'login_success_api',
            'description' => 'User logged in via mobile app',
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);

        return response()->json([
            'message' => 'Login berhasil',
            'token' => $token,
            'user' => $user
        ]);
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

        $token = $user->createToken('mobile_app')->plainTextToken;

        ActivityLog::create([
            'user_id' => $user->id,
            'event_type' => 'register_success_api',
            'description' => 'User registered via mobile app',
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);

        return response()->json([
            'message' => 'Registrasi berhasil',
            'token' => $token,
            'user' => $user
        ], 201);
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        ActivityLog::create([
            'user_id' => $request->user()->id,
            'event_type' => 'logout_api',
            'description' => 'User logged out from mobile app',
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);

        return response()->json([
            'message' => 'Berhasil keluar'
        ]);
    }

    public function googleLogin(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'name' => 'required|string',
            'google_id' => 'required|string',
            'avatar' => 'nullable|string',
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user) {
            // Create new user (incomplete profile)
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'google_id' => $request->google_id,
                'password' => Hash::make(Str::random(16)),
                'avatar' => $request->avatar,
                'role' => 'Warga',
            ]);
        }

        $token = $user->createToken('mobile_app')->plainTextToken;

        // Check if profile is complete
        $isComplete = !empty($user->phone) && !empty($user->address) && !empty($user->emergency_phone);

        ActivityLog::create([
            'user_id' => $user->id,
            'event_type' => 'google_login_api',
            'description' => 'User logged in via Google',
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);

        return response()->json([
            'message' => 'Login Google berhasil',
            'token' => $token,
            'user' => $user,
            'is_complete' => $isComplete
        ]);
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

        $user = $request->user();
        $user->update([
            'phone' => $request->phone,
            'address' => $request->address,
            'latitude' => $request->latitude,
            'longitude' => $request->longitude,
            'emergency_phone' => $request->emergency_phone,
        ]);

        ActivityLog::create([
            'user_id' => $user->id,
            'event_type' => 'complete_profile_api',
            'description' => 'User completed profile after social login',
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);

        return response()->json([
            'message' => 'Profil berhasil dilengkapi',
            'user' => $user
        ]);
    }

    public function forgotPassword(Request $request, WhatsAppService $waService)
    {
        $request->validate([
            'email' => 'required|email|exists:users,email',
            'method' => 'required|in:email,whatsapp'
        ]);

        $user = User::where('email', $request->email)->first();
        $token = Str::random(64);

        DB::table('password_reset_tokens')->updateOrInsert(
            ['email' => $request->email],
            [
                'token' => $token,
                'created_at' => Carbon::now()
            ]
        );

        if ($request->method === 'whatsapp') {
            if (!$user->phone) {
                return response()->json(['message' => 'Nomor WhatsApp tidak terdaftar.'], 422);
            }

            // Deep Link for Mobile App
            $resetUrl = "watersense://reset-password?token=" . $token . "&email=" . $request->email;
            
            $message = "🛡️ *WaterSense Security Protocol*\n\n";
            $message .= "Halo *" . $user->name . "*,\n\n";
            $message .= "Kami menerima permintaan reset password. Silakan klik link di bawah ini untuk membuka aplikasi:\n\n";
            $message .= $resetUrl . "\n\n";
            $message .= "⚠️ *Link ini akan otomatis membuka aplikasi WaterSense.*";

            $sent = $waService->sendMessage($user->phone, $message);
            if (!$sent) {
                return response()->json(['message' => 'Gagal mengirim WhatsApp.'], 500);
            }

            return response()->json(['message' => 'Link reset telah dikirim ke WhatsApp.']);
        } else {
            Mail::to($request->email)->send(new PasswordResetMail($token, $request->email));
            return response()->json(['message' => 'Link reset telah dikirim ke email.']);
        }
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

        if (!$resetData || Carbon::parse($resetData->created_at)->addMinutes(60)->isPast()) {
            return response()->json(['message' => 'Token tidak valid atau kadaluwarsa.'], 422);
        }

        $user = User::where('email', $request->email)->first();
        $user->update([
            'password' => Hash::make($request->password),
            'must_change_password' => false
        ]);

        DB::table('password_reset_tokens')->where(['email' => $request->email])->delete();

        return response()->json(['message' => 'Password berhasil direset.']);
    }

    public function me(Request $request)
    {
        return response()->json($request->user());
    }
}
