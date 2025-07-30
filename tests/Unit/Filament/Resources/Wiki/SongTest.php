<?php

declare(strict_types=1);

use App\Enums\Auth\CrudPermission;
use App\Enums\Auth\SpecialPermission;
use App\Filament\Actions\Base\CreateAction;
use App\Filament\Actions\Base\DeleteAction;
use App\Filament\Actions\Base\EditAction;
use App\Filament\Actions\Base\ForceDeleteAction;
use App\Filament\Actions\Base\RestoreAction;
use App\Filament\Resources\Wiki\Song;
use App\Models\Auth\User;
use App\Models\Wiki\Song as SongModel;
use Livewire\Livewire;

test('render index page', function () {
    $user = User::factory()
        ->withPermissions(
            SpecialPermission::VIEW_FILAMENT->value,
            CrudPermission::VIEW->format(SongModel::class)
        )
        ->createOne();

    $this->actingAs($user);

    $records = SongModel::factory()->count(10)->create();

    $this->get(Song::getUrl('index'))
        ->assertSuccessful();

    Livewire::test(getIndexPage(Song::class))
        ->assertCanSeeTableRecords($records);
});

test('render view page', function () {
    $user = User::factory()
        ->withPermissions(
            SpecialPermission::VIEW_FILAMENT->value,
            CrudPermission::VIEW->format(SongModel::class)
        )
        ->createOne();

    $this->actingAs($user);

    $record = SongModel::factory()->createOne();

    $this->get(Song::getUrl('view', ['record' => $record]))
        ->assertSuccessful();
});

test('mount create action', function () {
    $user = User::factory()
        ->withPermissions(
            SpecialPermission::VIEW_FILAMENT->value,
            CrudPermission::CREATE->format(SongModel::class)
        )
        ->createOne();

    $this->actingAs($user);

    Livewire::test(getIndexPage(Song::class))
        ->mountAction(CreateAction::class)
        ->assertActionMounted(CreateAction::class);
});

test('mount edit action', function () {
    $user = User::factory()
        ->withPermissions(
            SpecialPermission::VIEW_FILAMENT->value,
            CrudPermission::UPDATE->format(SongModel::class)
        )
        ->createOne();

    $this->actingAs($user);

    $record = SongModel::factory()->createOne();

    Livewire::test(getIndexPage(Song::class))
        ->mountAction(EditAction::class, ['record' => $record])
        ->assertActionMounted(EditAction::class);
});

test('user cannot create record', function () {
    Livewire::test(getIndexPage(Song::class))
        ->assertActionHidden(CreateAction::class);
});

test('user cannot edit record', function () {
    $record = SongModel::factory()->createOne();

    Livewire::test(getIndexPage(Song::class))
        ->assertActionHidden(EditAction::class, ['record' => $record->getKey()]);
});

test('user cannot delete record', function () {
    $record = SongModel::factory()->createOne();

    Livewire::test(getViewPage(Song::class), ['record' => $record->getKey()])
        ->assertActionHidden(DeleteAction::class);

    Livewire::test(getIndexPage(Song::class))
        ->assertActionHidden(DeleteAction::class, ['record' => $record->getKey()]);
});

test('user cannot restore record', function () {
    $record = SongModel::factory()->createOne();

    $record->delete();

    Livewire::test(getViewPage(Song::class), ['record' => $record->getKey()])
        ->assertActionHidden(RestoreAction::class);

    Livewire::test(getIndexPage(Song::class))
        ->assertActionHidden(RestoreAction::class, ['record' => $record->getKey()]);
});

test('user cannot force delete record', function () {
    $record = SongModel::factory()->createOne();

    Livewire::test(getViewPage(Song::class), ['record' => $record->getKey()])
        ->assertActionHidden(ForceDeleteAction::class);

    Livewire::test(getIndexPage(Song::class))
        ->assertActionHidden(ForceDeleteAction::class, ['record' => $record->getKey()]);
});
