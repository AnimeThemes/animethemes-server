<?php

declare(strict_types=1);

namespace App\Filament\Dashboards;

use App\Enums\Auth\Role as RoleEnum;
use App\Models\Auth\User;
// use App\Filament\Widgets\Admin\ExceptionsTableWidget;
use Filament\Facades\Filament;
use Filament\Panel;
use Filament\Support\Icons\Heroicon;

class DeveloperDashboard extends BaseDashboard
{
    public static function getSlug(?Panel $panel = null): string
    {
        return 'dev';
    }

    public static function canAccess(): bool
    {
        return User::find(Filament::auth()->id())->hasRole(RoleEnum::ADMIN->value);
    }

    public static function getNavigationLabel(): string
    {
        return __('filament.dashboards.label.dev');
    }

    public static function getNavigationIcon(): Heroicon
    {
        return Heroicon::CodeBracket;
    }

    /**
     * @return class-string[]
     */
    public function getWidgets(): array
    {
        return [
            // ExceptionsTableWidget::class,
        ];
    }
}
