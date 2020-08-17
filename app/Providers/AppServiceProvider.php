<?php

namespace App\Providers;

use App\Models\Invitation;
use App\Observers\InvitationObserver;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Schema;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->loadViewsFrom(base_path('vendor/laravel/nova/resources/views'), 'nova');
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Schema::defaultStringLength(191);

        Blade::if('env', function ($environment) {
            return app()->environment($environment);
        });

        Invitation::observe(InvitationObserver::class);

        JsonResource::withoutWrapping();
    }
}
