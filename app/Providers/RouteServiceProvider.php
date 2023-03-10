<?php

declare(strict_types=1);

namespace App\Providers;

use App\Constants\Config\AudioConstants;
use App\Constants\Config\DumpConstants;
use App\Constants\Config\VideoConstants;
use App\Enums\Auth\SpecialPermission;
use App\Models\Auth\User;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Route;

/**
 * Class RouteServiceProvider.
 */
class RouteServiceProvider extends ServiceProvider
{
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

        $this->routes(function () {
            Route::middleware('web')
                ->domain(Config::get('web.url'))
                ->prefix(Config::get('web.path'))
                ->group(base_path('routes/web.php'));

            Route::middleware('web')
                ->domain(Config::get(VideoConstants::URL_QUALIFIED))
                ->prefix(Config::get(VideoConstants::PATH_QUALIFIED))
                ->group(base_path('routes/video.php'));

            Route::middleware('web')
                ->domain(Config::get(AudioConstants::URL_QUALIFIED))
                ->prefix(Config::get(AudioConstants::PATH_QUALIFIED))
                ->group(base_path('routes/audio.php'));

            Route::middleware('web')
                ->domain(Config::get(DumpConstants::URL_QUALIFIED))
                ->prefix(Config::get(DumpConstants::PATH_QUALIFIED))
                ->group(base_path('routes/dump.php'));

            Route::middleware('web')
                ->domain(Config::get(VideoConstants::SCRIPT_URL_QUALIFIED))
                ->prefix(Config::get(VideoConstants::SCRIPT_PATH_QUALIFIED))
                ->group(base_path('routes/script.php'));

            Route::middleware('api')
                ->domain(Config::get('api.url'))
                ->prefix(Config::get('api.path'))
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
        RateLimiter::for('api', function (Request $request) {
            // Allow the client to bypass API rate limiting
            $user = $request->user('sanctum');
            if ($user instanceof User && $user->can(SpecialPermission::BYPASS_API_RATE_LIMITER)) {
                return Limit::none();
            }

            return Limit::perMinute(90)->by(Auth::check() ? Auth::id() : $request->ip());
        });
    }
}
