<?php

declare(strict_types=1);

namespace App\Providers;

use App\Models\Auth\Team;
use App\Models\Auth\User;
use App\Nova\Metrics\Anime\AnimePerDay;
use App\Nova\Metrics\Anime\NewAnime;
use App\Nova\Metrics\Artist\ArtistsPerDay;
use App\Nova\Metrics\Artist\NewArtists;
use App\Nova\Metrics\Series\NewSeries;
use App\Nova\Metrics\Series\SeriesPerDay;
use App\Nova\Metrics\Video\NewVideos;
use App\Nova\Metrics\Video\VideosPerDay;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Gate;
use Illuminatech\NovaConfig\NovaConfig;
use Laravel\Nova\Nova;
use Laravel\Nova\NovaApplicationServiceProvider;

/**
 * Class NovaServiceProvider.
 */
class NovaServiceProvider extends NovaApplicationServiceProvider
{
    /**
     * Register the Nova routes.
     *
     * @return void
     *
     * @noinspection PhpMissingParentCallCommonInspection
     */
    protected function routes(): void
    {
        Nova::routes()->register();
    }

    /**
     * Register the Nova gate.
     *
     * This gate determines who can access Nova in non-local environments.
     *
     * @return void
     *
     * @noinspection PhpMissingParentCallCommonInspection
     */
    protected function gate(): void
    {
        Gate::define('viewNova', function (User $user) {
            $novaTeam = Team::query()->find(Config::get('teams.nova'));

            return $user->isCurrentTeam($novaTeam);
        });

        // Only admins can see audit logs
        Gate::define('audit', fn (User $user) => $user->hasCurrentTeamPermission('audit:read'));
        Gate::define('audit_restore', fn (User $user) => $user->hasCurrentTeamPermission('audit:restore'));
    }

    /**
     * Get the cards that should be displayed on the default Nova dashboard.
     *
     * @return array
     */
    protected function cards(): array
    {
        return array_merge(
            parent::cards(),
            [
                (new NewVideos())->width('1/4'),
                (new NewAnime())->width('1/4'),
                (new NewArtists())->width('1/4'),
                (new NewSeries())->width('1/4'),

                (new VideosPerDay())->width('1/4'),
                (new AnimePerDay())->width('1/4'),
                (new ArtistsPerDay())->width('1/4'),
                (new SeriesPerDay())->width('1/4'),
            ]
        );
    }

    /**
     * Get the tools that should be listed in the Nova sidebar.
     *
     * @return array
     *
     * @noinspection PhpMissingParentCallCommonInspection
     */
    public function tools(): array
    {
        return [
            (new NovaConfig())
                ->canSee(fn (Request $request) => $request->user()->hasCurrentTeamPermission('config:update')),
        ];
    }
}
