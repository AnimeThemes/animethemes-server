<?php

declare(strict_types=1);

namespace App\Filament\Dashboards;

use Filament\Pages\Dashboard;

/**
 * Class BaseDashboard.
 */
abstract class BaseDashboard extends Dashboard
{
    /**
     * Get the route path for the dashboard.
     *
     * @return string
     */
    public static function getRoutePath(): string
    {
        return 'dashboards/'.static::getSlug();
    }
}