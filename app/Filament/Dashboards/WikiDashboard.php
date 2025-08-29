<?php

declare(strict_types=1);

namespace App\Filament\Dashboards;

use App\Filament\Widgets\Wiki\Anime\AnimeChart;
use App\Filament\Widgets\Wiki\Artist\ArtistChart;
use App\Filament\Widgets\Wiki\Series\SeriesChart;
use App\Filament\Widgets\Wiki\Video\VideoChart;
use Filament\Panel;
use Filament\Support\Icons\Heroicon;

class WikiDashboard extends BaseDashboard
{
    /**
     * Get the slug used to the dashboard route path.
     */
    public static function getSlug(?Panel $panel = null): string
    {
        return 'wiki';
    }

    /**
     * Get the displayed label for the dashboard.
     */
    public static function getNavigationLabel(): string
    {
        return __('filament.dashboards.label.wiki');
    }

    /**
     * Get the icon for the dashboard.
     */
    public static function getNavigationIcon(): Heroicon
    {
        return Heroicon::ChartBar;
    }

    /**
     * Get the widgets available for the dashboard.
     *
     * @return class-string[]
     */
    public function getWidgets(): array
    {
        return [
            AnimeChart::class,
            ArtistChart::class,
            SeriesChart::class,
            VideoChart::class,
        ];
    }
}
