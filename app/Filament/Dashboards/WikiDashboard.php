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
    public static function getSlug(?Panel $panel = null): string
    {
        return 'wiki';
    }

    public static function getNavigationLabel(): string
    {
        return __('filament.dashboards.label.wiki');
    }

    public static function getNavigationIcon(): Heroicon
    {
        return Heroicon::ChartBar;
    }

    /**
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
