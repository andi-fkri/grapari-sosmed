<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AuthController extends Controller
{
    public function showLogin(Request $request): View|RedirectResponse
    {
        if ($request->session()->get('grapari_authenticated')) {
            return redirect()->route('dashboard');
        }

        return view('login');
    }

    public function login(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'username' => 'required|string',
            'password' => 'required|string',
        ]);

        if (strtolower(trim($validated['username'])) === 'admin' && $validated['password'] === 'admin') {
            $request->session()->regenerate();
            $request->session()->put('grapari_authenticated', true);
            $request->session()->put('grapari_user', 'Admin');

            return redirect()->route('dashboard');
        }

        return back()
            ->withInput($request->only('username'))
            ->withErrors([
                'login' => 'Username atau password salah. Gunakan username & password yang benar.',
            ]);
    }

    public function logout(Request $request): RedirectResponse
    {
        $request->session()->forget(['grapari_authenticated', 'grapari_user']);
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')
            ->with('status', 'Anda telah berhasil keluar dari sistem.');
    }
}
