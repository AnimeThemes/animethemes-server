<?php

declare(strict_types=1);

namespace App\Filament\Dashboards;

use App\Enums\Auth\Role as RoleEnum;
use App\Filament\Widgets\Auth\UserChart;
use App\Filament\Widgets\List\ExternalProfileChart;
use App\Filament\Widgets\List\PlaylistChart;
use App\Filament\Widgets\List\PlaylistTrackChart;
use App\Models\Auth\User;
use Filament\Facades\Filament;

/**
 * Class AdminDashboard.
 */
class AdminDashboard extends BaseDashboard
{
    /**
     * Get the slug used to the dashboard route path.
     *
     * @return string
     */
    public static function getSlug(): string
    {
        return 'admin';
    }

    /**
     * Determine if the user can access the dashboard.
     *
     * @return bool
     */
    public static function canAccess(): bool
    {
        return User::find(Filament::auth()->id())->hasRole(RoleEnum::ADMIN->value);
    }

    /**
     * Get the displayed label for the dashboard.
     *
     * @return string
     */
    public static function getNavigationLabel(): string
    {
        return __('filament.dashboards.label.admin');
    }

    /**
     * Get the icon for the dashboard.
     *
     * @return string
     */
    public static function getNavigationIcon(): string
    {
        return __('filament-icons.dashboards.admin');;
    }

    /**
     * Get the widgets available for the dashboard.
     *
     * @return class-string[]
     */
    public function getWidgets(): array
    {
        return [
            UserChart::class,
            ExternalProfileChart::class,
            PlaylistChart::class,
            PlaylistTrackChart::class,
        ];
    }
}