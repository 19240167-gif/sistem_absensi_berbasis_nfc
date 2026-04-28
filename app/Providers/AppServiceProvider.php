<?php

namespace App\Providers;

use App\Http\Responses\DemoLogoutResponse;
use Filament\Http\Responses\Auth\LogoutResponse as FilamentLogoutResponse;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        $this->app->bind(FilamentLogoutResponse::class, DemoLogoutResponse::class);
    }
}
