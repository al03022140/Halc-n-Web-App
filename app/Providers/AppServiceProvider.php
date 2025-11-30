<?php

namespace App\Providers;

use App\Models\Order;
use Illuminate\Support\Facades\Route;
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
        // Registrar alias de middleware para control de roles
        if ($this->app->runningInConsole() || $this->app->bound('router')) {
            $router = $this->app->make('router');
            $router->aliasMiddleware('role', \App\Http\Middleware\EnsureRole::class);
        }

        if ($this->app->bound('router')) {
            Route::bind('order', function ($value) {
                return Order::withTrashed()->findOrFail($value);
            });
        }
    }
}
