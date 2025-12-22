<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Auth;
use App\Services\RisdaHashService;
use App\Auth\RisdaUserProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // Register RISDA Hash Service as singleton
        $this->app->singleton(RisdaHashService::class, function ($app) {
            return new RisdaHashService();
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Register custom Blade components
        \Illuminate\Support\Facades\Blade::component('layouts.dashboard', 'dashboard-layout');

        // Blade directive for media URLs: @mediaUrl($path)
        \Illuminate\Support\Facades\Blade::directive('mediaUrl', function ($expression) {
            return "<?php echo \\App\\Support\\Media::url($expression); ?>";
        });

        // Register RISDA custom user provider
        Auth::provider('risda', function ($app, array $config) {
            return new RisdaUserProvider($app->make(RisdaHashService::class), $config['model']);
        });
    }
}
