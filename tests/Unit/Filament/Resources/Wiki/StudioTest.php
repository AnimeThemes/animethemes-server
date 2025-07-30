<?php

declare(strict_types=1);

use App\Enums\Auth\CrudPermission;
use App\Enums\Auth\SpecialPermission;
use App\Filament\Actions\Base\CreateAction;
use App\Filament\Actions\Base\DeleteAction;
use App\Filament\Actions\Base\EditAction;
use App\Filament\Actions\Base\ForceDeleteAction;
use App\Filament\Actions\Base\RestoreAction;
use App\Filament\Resources\Wiki\Studio;
use App\Models\Auth\User;
use App\Models\Wiki\Studio as StudioModel;
use Livewire\Livewire;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\get;

test('render index page', function () {
    $user = User::factory()
        ->withPermissions(
            SpecialPermission::VIEW_FILAMENT->value,
            CrudPermission::VIEW->format(StudioModel::class)
        )
        ->createOne();

    actingAs($user);

    $records = StudioModel::factory()->count(10)->create();

    get(Studio::getUrl('index'))
        ->assertSuccessful();

    Livewire::test(getIndexPage(Studio::class))
        ->assertCanSeeTableRecords($records);
});

test('render view page', function () {
    $user = User::factory()
        ->withPermissions(
            SpecialPermission::VIEW_FILAMENT->value,
            CrudPermission::VIEW->format(StudioModel::class)
        )
        ->createOne();

    actingAs($user);

    $record = StudioModel::factory()->createOne();

    get(Studio::getUrl('view', ['record' => $record]))
        ->assertSuccessful();
});

test('mount create action', function () {
    $user = User::factory()
        ->withPermissions(
            SpecialPermission::VIEW_FILAMENT->value,
            CrudPermission::CREATE->format(StudioModel::class)
        )
        ->createOne();

    actingAs($user);

    Livewire::test(getIndexPage(Studio::class))
        ->mountAction(CreateAction::class)
        ->assertActionMounted(CreateAction::class);
});

test('mount edit action', function () {
    $user = User::factory()
        ->withPermissions(
            SpecialPermission::VIEW_FILAMENT->value,
            CrudPermission::UPDATE->format(StudioModel::class)
        )
        ->createOne();

    actingAs($user);

    $record = StudioModel::factory()->createOne();

    Livewire::test(getIndexPage(Studio::class))
        ->mountAction(EditAction::class, ['record' => $record])
        ->assertActionMounted(EditAction::class);
});

test('user cannot create record', function () {
    Livewire::test(getIndexPage(Studio::class))
        ->assertActionHidden(CreateAction::class);
});

test('user cannot edit record', function () {
    $record = StudioModel::factory()->createOne();

    Livewire::test(getIndexPage(Studio::class))
        ->assertActionHidden(EditAction::class, ['record' => $record->getKey()]);
});

test('user cannot delete record', function () {
    $record = StudioModel::factory()->createOne();

    Livewire::test(getViewPage(Studio::class), ['record' => $record->getKey()])
        ->assertActionHidden(DeleteAction::class);

    Livewire::test(getIndexPage(Studio::class))
        ->assertActionHidden(DeleteAction::class, ['record' => $record->getKey()]);
});

test('user cannot restore record', function () {
    $record = StudioModel::factory()->createOne();

    $record->delete();

    Livewire::test(getViewPage(Studio::class), ['record' => $record->getKey()])
        ->assertActionHidden(RestoreAction::class);

    Livewire::test(getIndexPage(Studio::class))
        ->assertActionHidden(RestoreAction::class, ['record' => $record->getKey()]);
});

test('user cannot force delete record', function () {
    $record = StudioModel::factory()->createOne();

    Livewire::test(getViewPage(Studio::class), ['record' => $record->getKey()])
        ->assertActionHidden(ForceDeleteAction::class);

    Livewire::test(getIndexPage(Studio::class))
        ->assertActionHidden(ForceDeleteAction::class, ['record' => $record->getKey()]);
});
