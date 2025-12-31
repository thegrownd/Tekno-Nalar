<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\EmailVerificationCode;
use App\Mail\OtpMail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules\Password;
use Tymon\JWTAuth\Exceptions\JWTException;
use App\Services\ActivityLogService;
use Laravel\Socialite\Facades\Socialite;
use Carbon\Carbon;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'confirmed', Password::min(8)->mixedCase()->numbers()->symbols()],
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'email_verified' => false,
        ]);

        $this->sendOtp($user);

        return response()->json(['message' => 'Registrasi berhasil. Silakan cek email Anda untuk kode verifikasi.', 'email' => $user->email], 201);
    }

    protected function sendOtp($user)
    {
        $code = str_pad(mt_rand(0, 999999), 6, '0', STR_PAD_LEFT);
        
        EmailVerificationCode::create([
            'email' => $user->email,
            'code' => $code,
            'expiry_time' => Carbon::now()->addMinutes(15),
        ]);

        Mail::to($user->email)->send(new OtpMail($code));
    }

    public function verifyOtp(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:users,email',
            'code' => 'required|string|size:6',
        ]);

        $verification = EmailVerificationCode::where('email', $request->email)
            ->where('code', $request->code)
            ->where('is_used', false)
            ->where('expiry_time', '>', Carbon::now())
            ->first();

        if (!$verification) {
            return response()->json(['message' => 'Kode OTP salah atau kedaluwarsa.'], 400);
        }

        $user = User::where('email', $request->email)->first();
        $user->email_verified = true;
        $user->email_verified_at = Carbon::now();
        $user->save();

        $verification->is_used = true;
        $verification->save();

        // Auto login after verification
        $token = auth('api')->login($user);

        return $this->respondWithToken($token, $user);
    }

    public function resendOtp(Request $request)
    {
        $request->validate(['email' => 'required|email|exists:users,email']);

        $throttleKey = 'resend-otp:' . $request->email;

        if (RateLimiter::tooManyAttempts($throttleKey, 5)) {
            $seconds = RateLimiter::availableIn($throttleKey);
            return response()->json(['message' => 'Terlalu banyak permintaan. Coba lagi dalam ' . $seconds . ' detik.'], 429);
        }

        RateLimiter::hit($throttleKey, 3600); // 5 attempts per hour

        $user = User::where('email', $request->email)->first();

        if ($user->email_verified) {
            return response()->json(['message' => 'Email sudah terverifikasi.'], 400);
        }

        $this->sendOtp($user);

        return response()->json(['message' => 'Kode OTP baru telah dikirim.']);
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);

        $throttleKey = Str::lower($request->input('email')) . '|' . $request->ip();

        if (RateLimiter::tooManyAttempts($throttleKey, 5)) {
            $seconds = RateLimiter::availableIn($throttleKey);
            return response()->json(['message' => 'Terlalu banyak percobaan login. Silakan coba lagi dalam ' . $seconds . ' detik.'], 429);
        }

        try {
            if (! $token = auth('api')->attempt($credentials)) {
                RateLimiter::hit($throttleKey);
                if ($user = User::where('email', $request->input('email'))->first()) {
                     ActivityLogService::logAdminLogin($user, 'Failed login attempt');
                }
                return response()->json(['message' => 'Kredensial salah'], 401);
            }
        } catch (JWTException $e) {
            return response()->json(['message' => 'Tidak dapat membuat token'], 500);
        }

        $user = auth('api')->user();

        // Bypass email verification for admin users
        if (!$user->email_verified && !$user->is_admin) {
            auth('api')->logout();
            return response()->json(['message' => 'Email belum diverifikasi.', 'email' => $user->email, 'needs_verification' => true], 403);
        }

        RateLimiter::clear($throttleKey);

        if (!$user->is_active) {
            auth('api')->logout();
            return response()->json(['message' => 'Akun Anda dinonaktifkan. Silakan hubungi admin.'], 403);
        }

        if (method_exists($user, 'isSuperAdmin') && $user->isSuperAdmin()) {
            ActivityLogService::logAdminLogin($user, 'Super admin login');
        }

        $user->last_login_at = Carbon::now();
        $user->save();

        return $this->respondWithToken($token, $user);
    }

    public function redirectToGoogle()
    {
        try {
            $url = Socialite::driver('google')->stateless()->redirect()->getTargetUrl();
            return response()->json(['url' => $url]);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Gagal menginisialisasi Google Auth: ' . $e->getMessage()], 500);
        }
    }

    public function handleGoogleCallback(Request $request)
    {
        try {
            \Illuminate\Support\Facades\Log::info('Google Callback hit', ['request' => $request->all()]);
            
            try {
                $socialUser = Socialite::driver('google')->stateless()->user();
            } catch (\Exception $e) {
                \Illuminate\Support\Facades\Log::error('Socialite Error', ['message' => $e->getMessage()]);
                return response()->json(['message' => 'Gagal memvalidasi login Google. Silakan coba lagi.'], 401);
            }
            
            \Illuminate\Support\Facades\Log::info('Google User Retrieved', [
                'email' => $socialUser->getEmail(),
                'name' => $socialUser->getName(),
                'id' => $socialUser->getId()
            ]);

            $user = User::where('email', $socialUser->getEmail())->first();

            if (!$user) {
                \Illuminate\Support\Facades\Log::info('Creating new user from Google', ['email' => $socialUser->getEmail()]);
                $user = User::create([
                    'name' => $socialUser->getName(),
                    'email' => $socialUser->getEmail(),
                    'google_id' => $socialUser->getId(),
                    'profile_picture_url' => $socialUser->getAvatar(),
                    'email_verified' => true, // Google emails are verified
                    'password' => null, // No password for Google users
                    'is_active' => true, // Ensure new users are active
                ]);
            } else {
                \Illuminate\Support\Facades\Log::info('Updating existing user with Google data', ['id' => $user->id]);
                if (!$user->google_id) {
                    $user->google_id = $socialUser->getId();
                    $user->email_verified = true; // Trust Google
                }
                $user->profile_picture_url = $socialUser->getAvatar();
                $user->save();
            }

            if (!$user->is_active) {
                \Illuminate\Support\Facades\Log::warning('Login attempt by inactive user', ['email' => $user->email]);
                return response()->json(['message' => 'Akun Anda dinonaktifkan. Silakan hubungi admin.'], 403);
            }

            $token = auth('api')->login($user);
            
            $user->last_login_at = Carbon::now();
            $user->save();
            
            // Log successful login for admins
            if ($user->is_admin) {
                ActivityLogService::logAdminLogin($user, 'Google Login');
            }

            \Illuminate\Support\Facades\Log::info('Google Login Successful', ['user_id' => $user->id]);

            return $this->respondWithToken($token, $user);
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Google Auth Failed', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json(['message' => 'Gagal login dengan Google: ' . $e->getMessage()], 500);
        }
    }

    public function logout()
    {
        auth('api')->logout();

        return response()->json(['message' => 'Logout berhasil']);
    }

    protected function respondWithToken($token, $user)
    {
        return response()->json([
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => auth('api')->factory()->getTTL() * 60,
            'user' => $user,
            'is_super_admin' => method_exists($user, 'isSuperAdmin') ? $user->isSuperAdmin() : false,
        ]);
    }
}
