<?php

namespace App\Http\Controllers;

use App\Models\Grapari;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AuthController extends Controller
{
    /**
     * Map of the 10 GraPARI branch usernames to their short_name and default password.
     *
     * @var array<string, array{short_name: string, password: string}>
     */
    protected array $branchAccounts = [
        'manado' => ['short_name' => 'Manado', 'password' => 'manado'],
        'ternate' => ['short_name' => 'Ternate', 'password' => 'ternate'],
        'tomohon' => ['short_name' => 'Tomohon', 'password' => 'tomohon'],
        'bitung' => ['short_name' => 'Bitung', 'password' => 'bitung'],
        'airmadidi' => ['short_name' => 'Airmadidi', 'password' => 'airmadidi'],
        'tahuna' => ['short_name' => 'Tahuna', 'password' => 'tahuna'],
        'tobelo' => ['short_name' => 'Tobelo', 'password' => 'tobelo'],
        'obi' => ['short_name' => 'Obi', 'password' => 'obi'],
        'weda' => ['short_name' => 'Weda', 'password' => 'weda'],
        'labuha' => ['short_name' => 'Labuha', 'password' => 'labuha'],
    ];

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

        $rawUsername = strtolower(trim($validated['username']));
        $password = $validated['password'];

        // 1. Check Super Admin (username: admin, password: admin)
        if ($rawUsername === 'admin' && $password === 'admin') {
            $request->session()->regenerate();
            $request->session()->put('grapari_authenticated', true);
            $request->session()->put('grapari_role', 'admin');
            $request->session()->put('grapari_user', 'Admin Regional');
            $request->session()->forget('grapari_branch_id');

            return redirect()->route('dashboard');
        }

        // 2. Check 10 GraPARI Branch Users (accepts "manado", "graparimanado", "grapari_manado", or "grapari manado")
        $normalizedKey = preg_replace('/^grapari[\s_-]*/', '', $rawUsername) ?? $rawUsername;
        $normalizedKey = trim($normalizedKey);

        if (isset($this->branchAccounts[$normalizedKey])) {
            $account = $this->branchAccounts[$normalizedKey];
            $expectedPass = $account['password'];

            if (strtolower($password) === $expectedPass || $password === 'admin') {
                $grapari = Grapari::where('short_name', $account['short_name'])->first();

                if ($grapari) {
                    $request->session()->regenerate();
                    $request->session()->put('grapari_authenticated', true);
                    $request->session()->put('grapari_role', 'branch');
                    $request->session()->put('grapari_user', $grapari->name);
                    $request->session()->put('grapari_branch_id', $grapari->id);

                    return redirect()->route('dashboard');
                }
            }
        }

        return back()
            ->withInput($request->only('username'))
            ->withErrors([
                'login' => 'Username atau password salah. Gunakan akun Admin atau salah satu dari 10 akun GraPARI.',
            ]);
    }

    public function logout(Request $request): RedirectResponse
    {
        $request->session()->forget([
            'grapari_authenticated',
            'grapari_role',
            'grapari_user',
            'grapari_branch_id',
        ]);
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')
            ->with('status', 'Anda telah berhasil keluar dari sistem.');
    }
}
