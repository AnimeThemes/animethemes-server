<?php

declare(strict_types=1);

namespace App\Providers;

use App\Models\Auth\Team;
use App\Models\Auth\User;
use App\Nova\Dashboards\Main;
use App\Nova\Resources\Wiki\Anime\Synonym;
use App\Nova\Resources\Wiki\Anime\Theme;
use App\Nova\Resources\Wiki\Anime\Theme\Entry;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Gate;
use Laravel\Nova\Menu\Menu;
use Laravel\Nova\Menu\MenuGroup;
use Laravel\Nova\Menu\MenuItem;
use Laravel\Nova\Nova;
use Laravel\Nova\NovaApplicationServiceProvider;

/**
 * Class NovaServiceProvider.
 */
class NovaServiceProvider extends NovaApplicationServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot(): void
    {
        parent::boot();

        Nova::userMenu(function (Request $request, Menu $menu) {
            $profile = MenuItem::externalLink(__('Profile'), route('profile.show'));
            $menu->append($profile);

            return $menu;
        });

        Nova::mainMenu(function (Request $request, Menu $menu) {
            $animeMenu = MenuGroup::make(__('nova.anime'), [
                MenuItem::resource(Synonym::class),
                MenuItem::resource(Theme::class),
            ]);
            $menu->append(Arr::wrap($animeMenu));

            $themeMenu = MenuGroup::make(__('nova.themes'), [
                MenuItem::resource(Entry::class),
            ]);
            $menu->append(Arr::wrap($themeMenu));

            return $menu;
        });
    }

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
    }

    /**
     * Get the cards that should be displayed on the Nova dashboard.
     *
     * @return array
     *
     * @noinspection PhpMissingParentCallCommonInspection
     */
    protected function dashboards(): array
    {
        return [
            new Main(),
        ];
    }
}
