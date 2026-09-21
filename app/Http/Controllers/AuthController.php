<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Laravel\Socialite\Facades\Socialite;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;


class AuthController extends Controller
{
    public function showRegister()
    {
        return view('auth.register');
    }

    public function register(Request $request)
    {
        $data = $request->validate([
            'username' => ['required','string','min:3','max:50','alpha_dash','unique:users,username'],
            'email'    => ['required','email','max:255','unique:users,email'],
            'password' => ['required','string','min:6','max:255','confirmed'],
        ]);

        $user = User::create([
            'username' => $data['username'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'role' => User::ROLE_BUYER,
        ]);

        Auth::login($user);
        $request->session()->regenerate();

        return redirect()->route('home')
            ->with('success', 'Register berhasil. Kamu sudah login.');
    }

    public function showLogin()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $data = $request->validate([
            'login'    => ['required','string'],
            'password' => ['required','string'],
        ]);

        $login = $data['login'];
        $field = filter_var($login, FILTER_VALIDATE_EMAIL) ? 'email' : 'username';

        if (Auth::attempt(
            [$field => $login, 'password' => $data['password']],
            $request->boolean('remember')
        )) {
            $request->session()->regenerate();

            $user = Auth::user();

            // Kalau pakai is_active
            if (isset($user->is_active) && !$user->is_active) {
                Auth::logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();

                throw ValidationException::withMessages([
                    'login' => 'Akun kamu sedang dinonaktifkan.',
                ]);
            }

            // Redirect berdasarkan role
            if ($user->role === User::ROLE_ADMIN) {
                return redirect()->route('admin.dashboard')
                    ->with('success', 'Login admin berhasil.');
            }

            return redirect()->intended(route('home'))
                ->with('success', 'Login berhasil.');
        }

        throw ValidationException::withMessages([
            'login' => 'Login gagal. Username/email atau password salah.',
        ]);
    }

    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('home')
            ->with('success', 'Logout berhasil.');
    }

    public function googleRedirect()
    {
        return Socialite::driver('google')
            ->stateless() // penting kalau kamu gak pakai session state oauth
            ->redirect();
    }

    public function googleCallback(Request $request)
    {
        try {
            $googleUser = Socialite::driver('google')->stateless()->user();

            $email = (string) ($googleUser->getEmail() ?? '');
            $providerId = (string) ($googleUser->getId() ?? '');

            if ($email === '' || $providerId === '') {
                throw ValidationException::withMessages([
                    'login' => 'Gagal login Google. Data akun tidak valid.',
                ]);
            }

            $user = DB::transaction(function () use ($email, $providerId, $googleUser) {

                // 1) cari berdasarkan provider id (paling aman)
                $u = User::where('oauth_provider', 'google')
                    ->where('oauth_provider_id', $providerId)
                    ->first();

                if ($u) return $u;

                // 2) kalau belum ada, cari berdasarkan email (biar akun lama bisa link)
                $u = User::where('email', $email)->first();
                if ($u) {
                    $u->oauth_provider = 'google';
                    $u->oauth_provider_id = $providerId;
                    $u->save();
                    return $u;
                }

                // 3) create user baru
                $base = Str::slug((string) ($googleUser->getName() ?: 'user'));
                $base = $base !== '' ? $base : 'user';

                $username = $base;
                $i = 0;
                while (User::where('username', $username)->exists()) {
                    $i++;
                    $username = $base . $i;
                }

                return User::create([
                    'username' => $username,
                    'email' => $email,
                    'password' => Hash::make(Str::random(32)), // random, karena login via SSO
                    'role' => User::ROLE_BUYER,
                    'oauth_provider' => 'google',
                    'oauth_provider_id' => $providerId,
                ]);
            });

            // optional: block user nonaktif
            if (isset($user->is_active) && !$user->is_active) {
                throw ValidationException::withMessages([
                    'login' => 'Akun kamu sedang dinonaktifkan.',
                ]);
            }

            Auth::login($user);
            $request->session()->regenerate();

            return redirect()->intended(route('home'))->with('success', 'Login Google berhasil.');

        } catch (\Throwable $e) {
            Log::error('SSO_GOOGLE_ERROR', [
                'err' => $e->getMessage(),
            ]);

            throw ValidationException::withMessages([
                'login' => 'Login Google gagal. Coba lagi.',
            ]);
        }
    }

}
