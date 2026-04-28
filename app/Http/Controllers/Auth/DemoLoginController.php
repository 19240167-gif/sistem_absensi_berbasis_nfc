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
        return app()->environment('local') || env('DEMO_AUTH', false);
    }

    public function index()
    {
        if (! $this->demoEnabled()) {
            abort(403);
        }

        $users = User::with('role')->orderBy('name')->get();

        return view('auth.demo-login', compact('users'));
    }

    public function login(Request $request, User $user)
    {
        if (! $this->demoEnabled()) {
            abort(403);
        }

        Auth::login($user);

        return redirect()->intended('/admin');
    }
}
