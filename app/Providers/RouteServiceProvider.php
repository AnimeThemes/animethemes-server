<?php

declare(strict_types=1);

namespace App\Providers;

use App\Models\Wiki\Anime\AnimeSynonym;
use App\Models\Wiki\Anime\AnimeTheme;
use App\Models\Wiki\Anime\Theme\AnimeThemeEntry;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Route;

/**
 * Class RouteServiceProvider.
 */
class RouteServiceProvider extends ServiceProvider
{
    /**
     * The path to the "home" route for your application.
     *
     * This is used by Laravel authentication to redirect users after login.
     *
     * @var string
     */
    public const HOME = '/dashboard';

    /**
     * Define your route model bindings, pattern filters, etc.
     *
     * @return void
     *
     * @noinspection PhpMissingParentCallCommonInspection
     */
    public function boot(): void
    {
        $this->configureRateLimiting();

        // Anime Resources
        Route::model('animesynonym', AnimeSynonym::class);
        Route::model('animetheme', AnimeTheme::class);

        // Anime Theme Resources
        Route::model('animethemeentry', AnimeThemeEntry::class);

        $this->routes(function () {
            Route::middleware('web')
                ->group(base_path('routes/web.php'));

            Route::prefix('api')
                ->middleware('api')
                ->as('api.')
                ->group(base_path('routes/api.php'));
        });
    }

    /**
     * Configure the rate limiters for the application.
     *
     * @return void
     */
    protected function configureRateLimiting(): void
    {
        RateLimiter::for(
            'api',
            fn (Request $request) => Limit::perMinute(90)->by(Auth::check() ? Auth::id() : $request->ip())
        );
    }
}
