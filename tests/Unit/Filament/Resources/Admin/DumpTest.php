<?php

declare(strict_types=1);

use App\Enums\Auth\CrudPermission;
use App\Enums\Auth\SpecialPermission;
use App\Filament\Actions\Base\DeleteAction;
use App\Filament\Actions\Base\EditAction;
use App\Filament\Resources\Admin\Dump;
use App\Models\Admin\Dump as DumpModel;
use App\Models\Auth\User;
use Filament\Actions\Testing\TestAction;
use Livewire\Livewire;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\get;

test('render index page', function () {
    $user = User::factory()
        ->withPermissions(
            SpecialPermission::VIEW_FILAMENT->value,
            CrudPermission::VIEW->format(DumpModel::class)
        )
        ->createOne();

    actingAs($user);

    $records = DumpModel::factory()->count(10)->create();

    get(Dump::getUrl('index'))
        ->assertSuccessful();

    Livewire::test(getIndexPage(Dump::class))
        ->assertCanSeeTableRecords($records);
});

test('user cannot edit record', function () {
    $record = DumpModel::factory()->createOne();

    Livewire::test(getIndexPage(Dump::class))
        ->assertActionDoesNotExist(TestAction::make(EditAction::getDefaultName())->table($record));
});

test('user cannot delete record', function () {
    $record = DumpModel::factory()->createOne();

    Livewire::test(getIndexPage(Dump::class))
        ->assertActionDoesNotExist(TestAction::make(DeleteAction::getDefaultName())->table($record));
});
