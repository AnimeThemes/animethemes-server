<?php

declare(strict_types=1);

use App\Enums\Auth\CrudPermission;
use App\Enums\Auth\SpecialPermission;
use App\Filament\Actions\Base\CreateAction;
use App\Filament\Actions\Base\DeleteAction;
use App\Filament\Actions\Base\EditAction;
use App\Filament\Actions\Base\ForceDeleteAction;
use App\Filament\Actions\Base\RestoreAction;
use App\Filament\Resources\Wiki\Artist;
use App\Models\Auth\User;
use App\Models\Wiki\Artist as ArtistModel;
use Livewire\Livewire;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\get;

test('render index page', function () {
    $user = User::factory()
        ->withPermissions(
            SpecialPermission::VIEW_FILAMENT->value,
            CrudPermission::VIEW->format(ArtistModel::class)
        )
        ->createOne();

    actingAs($user);

    $records = ArtistModel::factory()->count(10)->create();

    get(Artist::getUrl('index'))
        ->assertSuccessful();

    Livewire::test(getIndexPage(Artist::class))
        ->assertCanSeeTableRecords($records);
});

test('render view page', function () {
    $user = User::factory()
        ->withPermissions(
            SpecialPermission::VIEW_FILAMENT->value,
            CrudPermission::VIEW->format(ArtistModel::class)
        )
        ->createOne();

    actingAs($user);

    $record = ArtistModel::factory()->createOne();

    get(Artist::getUrl('view', ['record' => $record]))
        ->assertSuccessful();
});

test('mount create action', function () {
    $user = User::factory()
        ->withPermissions(
            SpecialPermission::VIEW_FILAMENT->value,
            CrudPermission::CREATE->format(ArtistModel::class)
        )
        ->createOne();

    actingAs($user);

    Livewire::test(getIndexPage(Artist::class))
        ->mountAction(CreateAction::class)
        ->assertActionMounted(CreateAction::class);
});

test('mount edit action', function () {
    $user = User::factory()
        ->withPermissions(
            SpecialPermission::VIEW_FILAMENT->value,
            CrudPermission::UPDATE->format(ArtistModel::class)
        )
        ->createOne();

    actingAs($user);

    $record = ArtistModel::factory()->createOne();

    Livewire::test(getIndexPage(Artist::class))
        ->mountAction(EditAction::class, ['record' => $record])
        ->assertActionMounted(EditAction::class);
});

test('user cannot create record', function () {
    Livewire::test(getIndexPage(Artist::class))
        ->assertActionHidden(CreateAction::class);
});

test('user cannot edit record', function () {
    $record = ArtistModel::factory()->createOne();

    Livewire::test(getIndexPage(Artist::class))
        ->assertActionHidden(EditAction::class, ['record' => $record->getKey()]);
});

test('user cannot delete record', function () {
    $record = ArtistModel::factory()->createOne();

    Livewire::test(getViewPage(Artist::class), ['record' => $record->getKey()])
        ->assertActionHidden(DeleteAction::class);

    Livewire::test(getIndexPage(Artist::class))
        ->assertActionHidden(DeleteAction::class, ['record' => $record->getKey()]);
});

test('user cannot restore record', function () {
    $record = ArtistModel::factory()->createOne();

    $record->delete();

    Livewire::test(getViewPage(Artist::class), ['record' => $record->getKey()])
        ->assertActionHidden(RestoreAction::class);

    Livewire::test(getIndexPage(Artist::class))
        ->assertActionHidden(RestoreAction::class, ['record' => $record->getKey()]);
});

test('user cannot force delete record', function () {
    $record = ArtistModel::factory()->createOne();

    Livewire::test(getViewPage(Artist::class), ['record' => $record->getKey()])
        ->assertActionHidden(ForceDeleteAction::class);

    Livewire::test(getIndexPage(Artist::class))
        ->assertActionHidden(ForceDeleteAction::class, ['record' => $record->getKey()]);
});
