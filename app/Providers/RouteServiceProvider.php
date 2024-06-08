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
        RateLimiter::for('api', function (Request $request) {
            $user = $request->user('sanctum');
            $ip = $request->ip();
            $forwardedIp = $request->header('x-forwarded-ip');

            // (If request is from client and no forwarded ip) or (the user logged in has permission to bypass API rate limiting)
            if (($ip === '127.0.0.1' && !$forwardedIp) || ($user instanceof User && $user->can(SpecialPermission::BYPASS_API_RATE_LIMITER->value))) {
                return Limit::none();
            }

            // Check if request is from client to prevent users from using forwarded ip
            if ($ip === '127.0.0.1' && $forwardedIp) {
                $ip = $forwardedIp;
            }

            return Limit::perMinute(90)->by(Auth::check() ? Auth::id() : $ip);
        });

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
}
