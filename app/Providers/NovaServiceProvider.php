<?php

declare(strict_types=1);

namespace App\Providers;

use App\Models\Auth\User;
use App\Nova\Dashboards\Main;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Laravel\Nova\LogViewer\LogViewer;
use Laravel\Nova\Menu\Menu;
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

        Nova::footer(fn () => '');
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
        Gate::define('viewNova', fn (User $user) => $user->can('view nova'));
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
            (new LogViewer())
                ->canSee(fn (Request $request) => $request->user()->can('view logs')),
        ];
    }
}
