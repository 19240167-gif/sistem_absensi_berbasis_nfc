<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DemoLoginController extends Controller
{
    protected function demoEnabled(): bool
    {
        return app()->environment('local') || config('app.demo_auth', false);
    }

    public function index()
    {
        if (! $this->demoEnabled()) {
            abort(403);
        }

        $users = User::with('role')
            ->where('is_active', true)
            ->whereHas('role', fn ($query) => $query->whereIn('slug', ['admin_tu', 'guru', 'siswa']))
            ->orderBy('name')
            ->get();

        return view('auth.demo-login', compact('users'));
    }

    public function login(Request $request, User $user)
    {
        if (! $this->demoEnabled()) {
            abort(403);
        }

        if (! $user->is_active || ! in_array($user->role?->slug, ['admin_tu', 'guru', 'siswa'], true)) {
            abort(403);
        }

        Auth::login($user);
        $request->session()->regenerate();

        if ($user->role?->slug === 'siswa') {
            return redirect()->intended('/student');
        }

        return redirect()->intended('/admin');
    }
}
