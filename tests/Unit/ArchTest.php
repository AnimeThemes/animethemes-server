<?php

declare(strict_types=1);

use Filament\Actions\Action;
use Filament\Actions\BulkAction;
use Filament\Pages\Dashboard;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Components\StateCasts\Contracts\StateCast;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Widgets\Widget;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Pivot;
use Illuminate\Support\ServiceProvider;

arch()
    ->expect('App')
    ->toUseStrictTypes()
    ->not->toUse(['die', 'dd', 'dump', 'var_dump']);

arch()
    ->expect('App\Concerns')
    ->toBeTraits();

arch()
    ->expect('App\Contracts')
    ->toBeInterfaces();

arch()
    ->expect('App\Enums')
    ->toBeEnums();

arch()
    ->expect('App\Features')
    ->toBeClasses()
    ->toHaveMethod('resolve');

arch()
    ->expect('App\Jobs')
    ->toBeClasses()
    ->toHaveMethod('handle')
    ->toImplement(ShouldQueue::class)
    ->ignoring('App\Jobs\Middleware');

arch()
    ->expect('App\*\Middleware')
    ->toBeClasses()
    ->toHaveMethod('handle');

arch()
    ->expect('App\Listeners')
    ->toBeClasses()
    ->toHaveMethod('handle');

arch()
    ->expect('App\Models')
    ->toBeClasses()
    ->toExtend(Model::class);

arch()
    ->expect('App\Pivots')
    ->toBeClasses()
    ->toExtend(Pivot::class);

arch()
    ->expect('App\Providers')
    ->toBeClasses()
    ->toExtend(ServiceProvider::class);

describe('filament', function () {
    arch()
        ->expect('App\Filament\Actions')
        ->toBeClasses()
        ->toExtend(Action::class);

    arch()
        ->expect('App\Filament\BulkActions')
        ->toBeClasses()
        ->toExtend(BulkAction::class);

    arch()
        ->expect('App\Filament\Dashboards')
        ->toBeClasses()
        ->toExtend(Dashboard::class);

    arch()
        ->expect('App\Filament\RelationManagers')
        ->toBeClasses()
        ->toExtend(RelationManager::class);

    arch()
        ->expect('App\Filament\StateCasts')
        ->toBeClasses()
        ->toImplement(StateCast::class);

    arch()
        ->expect('App\Filament\Tabs')
        ->toBeClasses()
        ->toExtend(Tab::class);

    arch()
        ->expect('App\Filament\Widgets')
        ->toBeClasses()
        ->toExtend(Widget::class);
});