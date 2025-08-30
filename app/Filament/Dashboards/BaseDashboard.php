<?php

declare(strict_types=1);

namespace App\Filament\Dashboards;

use Filament\Pages\Dashboard;
use Filament\Panel;

abstract class BaseDashboard extends Dashboard
{
    public static function getRoutePath(Panel $panel): string
    {
        return 'dashboards/'.static::getSlug();
    }

    public function getTitle(): string
    {
        return static::getNavigationLabel();
    }
}
