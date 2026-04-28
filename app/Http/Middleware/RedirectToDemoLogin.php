<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RedirectToDemoLogin
{
    public function handle(Request $request, Closure $next): Response
    {
        if ((app()->environment('local') || env('DEMO_AUTH')) && ! $request->user()) {
            if ($request->isMethod('get') && $request->is('admin/login', 'student/login', 'login')) {
                return redirect()->route('demo.login');
            }
        }

        return $next($request);
    }
}
