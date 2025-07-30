<?php

declare(strict_types=1);

use App\Enums\Auth\CrudPermission;
use App\Enums\Auth\SpecialPermission;
use App\Filament\Actions\Base\CreateAction;
use App\Filament\Actions\Base\DeleteAction;
use App\Filament\Actions\Base\EditAction;
use App\Filament\Actions\Base\ForceDeleteAction;
use App\Filament\Actions\Base\RestoreAction;
use App\Filament\Resources\Wiki\Series;
use App\Models\Auth\User;
use App\Models\Wiki\Series as SeriesModel;
use Livewire\Livewire;

test('render index page', function () {
    $user = User::factory()
        ->withPermissions(
            SpecialPermission::VIEW_FILAMENT->value,
            CrudPermission::VIEW->format(SeriesModel::class)
        )
        ->createOne();

    $this->actingAs($user);

    $records = SeriesModel::factory()->count(10)->create();

    $this->get(Series::getUrl('index'))
        ->assertSuccessful();

    Livewire::test(getIndexPage(Series::class))
        ->assertCanSeeTableRecords($records);
});

test('render view page', function () {
    $user = User::factory()
        ->withPermissions(
            SpecialPermission::VIEW_FILAMENT->value,
            CrudPermission::VIEW->format(SeriesModel::class)
        )
        ->createOne();

    $this->actingAs($user);

    $record = SeriesModel::factory()->createOne();

    $this->get(Series::getUrl('view', ['record' => $record]))
        ->assertSuccessful();
});

test('mount create action', function () {
    $user = User::factory()
        ->withPermissions(
            SpecialPermission::VIEW_FILAMENT->value,
            CrudPermission::CREATE->format(SeriesModel::class)
        )
        ->createOne();

    $this->actingAs($user);

    Livewire::test(getIndexPage(Series::class))
        ->mountAction(CreateAction::class)
        ->assertActionMounted(CreateAction::class);
});

test('mount edit action', function () {
    $user = User::factory()
        ->withPermissions(
            SpecialPermission::VIEW_FILAMENT->value,
            CrudPermission::UPDATE->format(SeriesModel::class)
        )
        ->createOne();

    $this->actingAs($user);

    $record = SeriesModel::factory()->createOne();

    Livewire::test(getIndexPage(Series::class))
        ->mountAction(EditAction::class, ['record' => $record])
        ->assertActionMounted(EditAction::class);
});

test('user cannot create record', function () {
    Livewire::test(getIndexPage(Series::class))
        ->assertActionHidden(CreateAction::class);
});

test('user cannot edit record', function () {
    $record = SeriesModel::factory()->createOne();

    Livewire::test(getIndexPage(Series::class))
        ->assertActionHidden(EditAction::class, ['record' => $record->getKey()]);
});

test('user cannot delete record', function () {
    $record = SeriesModel::factory()->createOne();

    Livewire::test(getViewPage(Series::class), ['record' => $record->getKey()])
        ->assertActionHidden(DeleteAction::class);

    Livewire::test(getIndexPage(Series::class))
        ->assertActionHidden(DeleteAction::class, ['record' => $record->getKey()]);
});

test('user cannot restore record', function () {
    $record = SeriesModel::factory()->createOne();

    $record->delete();

    Livewire::test(getViewPage(Series::class), ['record' => $record->getKey()])
        ->assertActionHidden(RestoreAction::class);

    Livewire::test(getIndexPage(Series::class))
        ->assertActionHidden(RestoreAction::class, ['record' => $record->getKey()]);
});

test('user cannot force delete record', function () {
    $record = SeriesModel::factory()->createOne();

    Livewire::test(getViewPage(Series::class), ['record' => $record->getKey()])
        ->assertActionHidden(ForceDeleteAction::class);

    Livewire::test(getIndexPage(Series::class))
        ->assertActionHidden(ForceDeleteAction::class, ['record' => $record->getKey()]);
});
