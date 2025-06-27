<?php

declare(strict_types=1);

namespace App\Filament\Widgets;

use Filament\Widgets\StatsOverviewWidget;

/**
 * Class BaseStatsWidget.
 */
class BaseStatsWidget extends StatsOverviewWidget
{
    protected static bool $isLazy = false;
}
