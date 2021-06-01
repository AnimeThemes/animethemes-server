<?php

declare(strict_types=1);

namespace App\Providers;

use App\Models\Team;
use App\Models\User;
use App\Nova\Metrics\AnimePerDay;
use App\Nova\Metrics\ArtistsPerDay;
use App\Nova\Metrics\NewAnime;
use App\Nova\Metrics\NewArtists;
use App\Nova\Metrics\NewSeries;
use App\Nova\Metrics\NewVideos;
use App\Nova\Metrics\SeriesPerDay;
use App\Nova\Metrics\VideosPerDay;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Gate;
use Illuminatech\NovaConfig\NovaConfig;
use Laravel\Nova\Nova;
use Laravel\Nova\NovaApplicationServiceProvider;

/**
 * Class NovaServiceProvider
 * @package App\Providers
 */
class NovaServiceProvider extends NovaApplicationServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        parent::boot();
    }

    /**
     * Register the Nova routes.
     *
     * @return void
     */
    protected function routes()
    {
        Nova::routes()->register();
    }

    /**
     * Register the Nova gate.
     *
     * This gate determines who can access Nova in non-local environments.
     *
     * @return void
     */
    protected function gate()
    {
        Gate::define('viewNova', function (User $user) {
            $novaTeam = Team::find(Config::get('nova.team'));

            return $user->isCurrentTeam($novaTeam);
        });

        // Only admins can see audit logs
        Gate::define('audit', function (User $user) {
            return $user->hasCurrentTeamPermission('audit:read');
        });
        Gate::define('audit_restore', function (User $user) {
            return $user->hasCurrentTeamPermission('audit:restore');
        });
    }

    /**
     * Get the cards that should be displayed on the default Nova dashboard.
     *
     * @return array
     */
    protected function cards(): array
    {
        return [
            (new NewVideos())->width('1/4'),
            (new NewAnime())->width('1/4'),
            (new NewArtists())->width('1/4'),
            (new NewSeries())->width('1/4'),

            (new VideosPerDay())->width('1/4'),
            (new AnimePerDay())->width('1/4'),
            (new ArtistsPerDay())->width('1/4'),
            (new SeriesPerDay())->width('1/4'),
        ];
    }

    /**
     * Get the extra dashboards that should be displayed on the Nova dashboard.
     *
     * @return array
     */
    protected function dashboards(): array
    {
        return [];
    }

    /**
     * Get the tools that should be listed in the Nova sidebar.
     *
     * @return array
     */
    public function tools(): array
    {
        return [
            (new NovaConfig())
                ->canSee(function (Request $request) {
                    return $request->user()->hasCurrentTeamPermission('config:update');
                }),
        ];
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }
}
