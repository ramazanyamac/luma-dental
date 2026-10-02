<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;
use App\Support\AdminUrl;

class AuthController extends Controller
{
    public function create(Request $request): View|RedirectResponse
    {
        if ($request->user()?->is_admin) {
            return redirect()->to(AdminUrl::route('admin.dashboard'));
        }

        return view('admin.auth.login');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'email' => ['required', 'string', 'email', 'max:254'],
            'password' => ['required', 'string'],
        ]);

        $email = strtolower(trim($validated['email']));

        $key = 'admin-login:' . hash(
            'sha256',
            $email . '|' . $request->ip()
        );

        if (RateLimiter::tooManyAttempts($key, 5)) {
            $seconds = RateLimiter::availableIn($key);

            throw ValidationException::withMessages([
                'email' => __('admin.auth.errors.throttled', [
                    'seconds' => $seconds,
                ]),
            ]);
        }

        $authenticated = Auth::attempt([
            'email' => $email,
            'password' => $validated['password'],
            'is_admin' => true,
        ]);

        if (!$authenticated) {
            RateLimiter::hit($key, 60);

            throw ValidationException::withMessages([
                'email' => __('admin.auth.errors.credentials'),
            ]);
        }

        RateLimiter::clear($key);

        $request->session()->forget('url.intended');

        return redirect()->to(AdminUrl::route('admin.dashboard'));
    }

    public function destroy(Request $request): RedirectResponse
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->to(AdminUrl::route('login'));
    }
}
