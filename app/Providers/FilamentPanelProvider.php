<?php

declare(strict_types=1);

namespace App\Providers;

use App\Filament\Providers\GlobalSearchScoutProvider;
use Awcodes\Recently\RecentlyPlugin;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Infolists\Components\Entry;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Schemas\Components\Section;
use Filament\Support\Assets\Js;
use Filament\Support\Colors\Color;
use Filament\Support\Enums\Width;
use Filament\Support\Facades\FilamentAsset;
use Filament\Tables\Columns\Column;
use Filament\Tables\Table;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\AuthenticateSession;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\Support\Facades\Config;
use Illuminate\View\Middleware\ShareErrorsFromSession;
//use Leandrocfe\FilamentApexCharts\FilamentApexChartsPlugin;

/**
 * Class FilamentPanelProvider.
 */
class FilamentPanelProvider extends PanelProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot(): void
    {
        FilamentAsset::register([
            Js::make('slug', asset('js/app/slug.js'))->module(false)
        ]);
    }

    public function register(): void
    {
        parent::register();

        Column::configureUsing(function (Column $column) {
            $column->placeholder('-');
            $column->sortable();
            $column->toggleable();
        });

        Entry::configureUsing(function (Entry $entry) {
            $entry->placeholder('-');
        });

        Section::configureUsing(function (Section $section) {
            $section->columnSpanFull();
        });

        Table::configureUsing(function (Table $table) {
            $table->deferFilters(false);
        });
    }

    public function panel(Panel $panel): Panel
    {
        return $panel
            ->default()
            ->id(Config::get('filament.path'))
            ->path(Config::get('filament.path'))
            ->domain(Config::get('filament.domain'))
            ->login()
            ->spa()
            ->unsavedChangesAlerts()
            ->brandLogo(asset('img/logo.svg'))
            ->darkModeBrandLogo(asset('img/gray-logo.svg'))
            ->brandLogoHeight('1.8rem')
            ->globalSearch(GlobalSearchScoutProvider::class)
            ->maxContentWidth(Width::Full)
            ->readOnlyRelationManagersOnResourceViewPagesByDefault(false)
            ->databaseNotifications()
            ->sidebarCollapsibleOnDesktop()
            ->colors([
                'primary' => Color::Violet,
            ])
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\\Filament\\Resources')
            ->discoverPages(in: app_path('Filament/Dashboards'), for: 'App\\Filament\\Dashboards')
            ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\\Filament\\Widgets')
            ->plugins([
                //FilamentApexChartsPlugin::make(),
                RecentlyPlugin::make(),
            ])
            ->middleware([
                EncryptCookies::class,
                AddQueuedCookiesToResponse::class,
                StartSession::class,
                AuthenticateSession::class,
                ShareErrorsFromSession::class,
                VerifyCsrfToken::class,
                SubstituteBindings::class,
                DisableBladeIconComponents::class,
                DispatchServingFilamentEvent::class,
            ])
            ->authMiddleware([
                Authenticate::class,
            ]);
    }
}
