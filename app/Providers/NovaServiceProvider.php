<?php

declare(strict_types=1);

namespace App\Providers;

use App\Models\Auth\User;
use App\Nova\Dashboards\Main;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Laravel\Nova\Badge;
use Laravel\Nova\Http\Requests\NovaRequest;
use Laravel\Nova\Menu\Menu;
use Laravel\Nova\Menu\MenuItem;
use Laravel\Nova\Menu\MenuSection;
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
            $novaRequest = NovaRequest::createFrom($request);

            $lenses = [];

            foreach (Nova::resourcesForNavigation($request) as $resourceClass) {
                $model = $resourceClass::newModel();

                $resource = new $resourceClass($model);

                foreach ($resource->availableLenses($novaRequest) as $lens) {
                    $count = $lens::criteria($model->newQuery())->count();

                    if ($count > 0) {
                        // We are not using the helper function to avoid redundant authorization
                        $lenses[] = MenuItem::make($lens->name())
                            ->path('/resources/'.$resourceClass::uriKey().'/lens/'.$lens->uriKey())
                            ->withBadge(Badge::make($count));
                    }
                }
            }

            $lensSection = MenuSection::make(__('nova.menu.main.section.lenses'), $lenses, 'video-camera')
                ->collapsable();

            $menu->items[] = $lensSection;

            return $menu;
        });

        // Enable breadcrumbs
        Nova::withBreadcrumbs();

        // Disable the footer
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
            (new Main())->showRefreshButton(),
        ];
    }
}
