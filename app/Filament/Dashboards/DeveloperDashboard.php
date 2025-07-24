<?php

declare(strict_types=1);

namespace App\Filament\Dashboards;

use App\Enums\Auth\Role as RoleEnum;
use App\Models\Auth\User;
// use App\Filament\Widgets\Admin\ExceptionsTableWidget;
use Filament\Facades\Filament;
use Filament\Panel;

class DeveloperDashboard extends BaseDashboard
{
    /**
     * Get the slug used to the dashboard route path.
     */
    public static function getSlug(?Panel $panel = null): string
    {
        return 'dev';
    }

    /**
     * Determine if the user can access the dashboard.
     */
    public static function canAccess(): bool
    {
        return User::find(Filament::auth()->id())->hasRole(RoleEnum::ADMIN->value);
    }

    /**
     * Get the displayed label for the dashboard.
     */
    public static function getNavigationLabel(): string
    {
        return __('filament.dashboards.label.dev');
    }

    /**
     * Get the icon for the dashboard.
     */
    public static function getNavigationIcon(): string
    {
        return __('filament-icons.dashboards.dev');
    }

    /**
     * Get the widgets available for the dashboard.
     *
     * @return class-string[]
     */
    public function getWidgets(): array
    {
        return [
            // ExceptionsTableWidget::class,
        ];
    }
}
