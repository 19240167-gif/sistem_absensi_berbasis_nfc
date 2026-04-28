<?php

namespace App\Http\Responses;

use Filament\Http\Responses\Auth\LogoutResponse;
use Illuminate\Http\RedirectResponse;

class DemoLogoutResponse extends LogoutResponse
{
    public function toResponse($request): RedirectResponse
    {
        if (app()->environment('local') || env('DEMO_AUTH')) {
            return redirect()->route('demo.login');
        }

        return redirect('/admin/login');
    }
}
