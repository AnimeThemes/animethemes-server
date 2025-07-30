<?php

declare(strict_types=1);

use App\Enums\Auth\CrudPermission;
use App\Enums\Auth\SpecialPermission;
use App\Filament\Actions\Base\CreateAction;
use App\Filament\Actions\Base\EditAction;
use App\Filament\Resources\List\Playlist;
use App\Models\Auth\User;
use App\Models\List\Playlist as PlaylistModel;
use Filament\Facades\Filament;
use Livewire\Livewire;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\get;

/**
 * Initial setup for the tests.
 */
beforeEach(function () {
    Filament::setServingStatus();
});

test('render index page', function () {
    $user = User::factory()
        ->withAdmin()
        ->withPermissions(
            SpecialPermission::VIEW_FILAMENT->value,
            CrudPermission::VIEW->format(PlaylistModel::class)
        )
        ->createOne();

    actingAs($user);

    $records = PlaylistModel::factory()->count(10)->create();

    get(Playlist::getUrl('index'))
        ->assertSuccessful();

    Livewire::test(getIndexPage(Playlist::class))
        ->assertCanSeeTableRecords($records);
});

test('render view page', function () {
    $user = User::factory()
        ->withAdmin()
        ->withPermissions(
            SpecialPermission::VIEW_FILAMENT->value,
            CrudPermission::VIEW->format(PlaylistModel::class)
        )
        ->createOne();

    actingAs($user);

    $record = PlaylistModel::factory()->createOne();

    get(Playlist::getUrl('view', ['record' => $record]))
        ->assertSuccessful();
});

test('mount create action', function () {
    $user = User::factory()
        ->withAdmin()
        ->withPermissions(
            SpecialPermission::VIEW_FILAMENT->value,
            CrudPermission::CREATE->format(PlaylistModel::class)
        )
        ->createOne();

    actingAs($user);

    Livewire::test(getIndexPage(Playlist::class))
        ->mountAction(CreateAction::class)
        ->assertActionMounted(CreateAction::class);
});

test('mount edit action', function () {
    $user = User::factory()
        ->withAdmin()
        ->withPermissions(
            SpecialPermission::VIEW_FILAMENT->value,
            CrudPermission::UPDATE->format(PlaylistModel::class),
        )
        ->createOne();

    actingAs($user);

    $record = PlaylistModel::factory()->createOne();

    Livewire::test(getIndexPage(Playlist::class))
        ->mountAction(EditAction::class, ['record' => $record])
        ->assertActionMounted(EditAction::class);
});
