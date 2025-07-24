<?php

declare(strict_types=1);

namespace App\Filament\Dashboards;

use Filament\Pages\Dashboard;
use Filament\Panel;

abstract class BaseDashboard extends Dashboard
{
    /**
     * Get the route path for the dashboard.
     *
     * @param  Panel  $panel
     * @return string
     */
    public static function getRoutePath(Panel $panel): string
    {
        return 'dashboards/'.static::getSlug();
    }

    /**
     * Get the title for the dashboard.
     *
     * @return string
     */
    public function getTitle(): string
    {
        return static::getNavigationLabel();
    }
}
