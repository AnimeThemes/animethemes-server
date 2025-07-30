<?php

declare(strict_types=1);

use App\Enums\Auth\CrudPermission;
use App\Enums\Auth\SpecialPermission;
use App\Filament\Actions\Base\DeleteAction;
use App\Filament\Actions\Base\EditAction;
use App\Filament\Actions\Base\ForceDeleteAction;
use App\Filament\Actions\Base\RestoreAction;
use App\Filament\Resources\Admin\Dump;
use App\Models\Admin\Dump as DumpModel;
use App\Models\Auth\User;
use Livewire\Livewire;

test('render index page', function () {
    $user = User::factory()
        ->withPermissions(
            SpecialPermission::VIEW_FILAMENT->value,
            CrudPermission::VIEW->format(DumpModel::class)
        )
        ->createOne();

    $this->actingAs($user);

    $records = DumpModel::factory()->count(10)->create();

    $this->get(Dump::getUrl('index'))
        ->assertSuccessful();

    Livewire::test(getIndexPage(Dump::class))
        ->assertCanSeeTableRecords($records);
});

test('user cannot edit record', function () {
    $record = DumpModel::factory()->createOne();

    Livewire::test(getIndexPage(Dump::class))
        ->assertActionHidden(EditAction::class, ['record' => $record->getKey()]);
});

test('user cannot delete record', function () {
    $record = DumpModel::factory()->createOne();

    Livewire::test(getIndexPage(Dump::class))
        ->assertActionHidden(DeleteAction::class, ['record' => $record->getKey()]);
});

test('user cannot restore record', function () {
    $record = DumpModel::factory()->createOne();

    Livewire::test(getIndexPage(Dump::class))
        ->assertActionHidden(RestoreAction::class, ['record' => $record->getKey()]);
});

test('user cannot force delete record', function () {
    $record = DumpModel::factory()->createOne();

    Livewire::test(getIndexPage(Dump::class))
        ->assertActionHidden(ForceDeleteAction::class, ['record' => $record->getKey()]);
});
