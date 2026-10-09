<?php

namespace App\Http\Controllers;

use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;

class AuthController extends Controller
{
    /**
     * Show the login form.
     */
    public function showLogin()
    {
        if (Auth::check()) {
            return redirect()->route('items.index');
        }
        return view('auth.login');
    }

    /**
     * Show the registration form.
     */
    public function showRegister()
    {
        if (Auth::check()) {
            return redirect()->route('items.index');
        }
        return view('auth.register');
    }

    /**
     * Handle manual registration.
     */
    public function register(RegisterRequest $request): RedirectResponse
    {
        $validated = $request->validated();

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'login_count' => 1,
            'last_login_at' => now(),
        ]);

        $this->generateAndStoreAvatar($user);

        Auth::login($user);

        return redirect()->route('items.index')->with('success', 'Pendaftaran berhasil dan Anda telah masuk!');
    }

    /**
     * Handle manual login.
     */
    public function login(LoginRequest $request): RedirectResponse
    {
        $credentials = $request->validated();

        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            $request->session()->regenerate();

            $user = Auth::user();
            $user->increment('login_count');
            $user->update(['last_login_at' => now()]);

            return redirect()->intended(route('items.index'))->with('success', 'Selamat datang kembali!');
        }

        return back()->withErrors([
            'email' => 'Email atau password tidak sesuai dengan data kami.',
        ])->onlyInput('email');
    }

    /**
     * Redirect to Google.
     * If google config is missing, fall back to mock simulation mode.
     */
    public function redirectToGoogle(Request $request): RedirectResponse
    {
        $clientId = config('services.google.client_id');

        // Check if user explicitly asked for mock or if credentials are not configured
        if (empty($clientId) || $request->has('mock')) {
            return redirect()->route('auth.google.mock');
        }

        try {
            return Socialite::driver('google')->redirect();
        } catch (\Exception $e) {
            // Fallback to mock if Socialite fails to redirect (e.g. driver not configured)
            return redirect()->route('auth.google.mock')
                ->with('warning', 'Google OAuth gagal dialihkan, masuk ke Mode Simulasi.');
        }
    }

    /**
     * Handle Google Callback.
     */
    public function handleGoogleCallback(): RedirectResponse
    {
        try {
            $googleUser = Socialite::driver('google')->user();
        } catch (\Exception $e) {
            return redirect()->route('login')
                ->with('error', 'Gagal masuk menggunakan Google: ' . $e->getMessage());
        }

        // Find or create the user
        $user = User::where('google_id', $googleUser->getId())
            ->orWhere('email', $googleUser->getEmail())
            ->first();

        if ($user) {
            // Update google_id if matched by email first
            if (empty($user->google_id)) {
                $user->update(['google_id' => $googleUser->getId()]);
            }
        } else {
            // Create a new user
            $user = User::create([
                'name' => $googleUser->getName() ?? 'Google User',
                'email' => $googleUser->getEmail(),
                'google_id' => $googleUser->getId(),
                'avatar' => $googleUser->getAvatar(),
                'login_count' => 0,
            ]);
        }

        Auth::login($user);

        $user->increment('login_count');
        $user->update(['last_login_at' => now()]);

        return redirect()->route('items.index')->with('success', 'Berhasil masuk menggunakan akun Google!');
    }

    /**
     * Google Login Simulator for easy local testing.
     */
    public function mockGoogleLogin(): RedirectResponse
    {
        $rand = rand(100, 999);
        $mockEmail = "mock.user{$rand}@gmail.com";
        
        $user = User::firstOrCreate(
            ['email' => $mockEmail],
            [
                'name' => "Simulated Google User {$rand}",
                'google_id' => "mock-google-id-{$rand}",
                'bio' => 'Ini adalah akun Google simulasi untuk pengujian lokal.',
                'login_count' => 0,
            ]
        );

        if (empty($user->avatar)) {
            $this->generateAndStoreAvatar($user);
        }

        Auth::login($user);

        $user->increment('login_count');
        $user->update(['last_login_at' => now()]);

        return redirect()->route('items.index')->with('success', 'Berhasil masuk menggunakan Akun Google Simulasi!');
    }

    /**
     * Generate a default avatar using Dicebear API and store it locally.
     */
    protected function generateAndStoreAvatar(User $user): void
    {
        try {
            $seed = urlencode($user->name);
            // Using adventurer style from Dicebear
            $url = "https://api.dicebear.com/9.x/adventurer/svg?seed={$seed}";
            
            $svgContent = @file_get_contents($url);
            
            if ($svgContent) {
                $fileName = 'avatars/' . Str::random(40) . '.svg';
                Storage::disk('public')->put($fileName, $svgContent);
                $user->update(['avatar' => $fileName]);
            }
        } catch (\Exception $e) {
            // Fallback: leave empty to use text-based initials view if download fails
        }
    }

    /**
     * Log the user out.
     */
    public function logout(Request $request): RedirectResponse
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')->with('success', 'Anda telah berhasil keluar.');
    }
}
