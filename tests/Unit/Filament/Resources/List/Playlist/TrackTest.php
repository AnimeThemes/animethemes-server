<?php

declare(strict_types=1);

use App\Enums\Auth\CrudPermission;
use App\Enums\Auth\SpecialPermission;
use App\Filament\Actions\Base\CreateAction;
use App\Filament\Actions\Base\EditAction;
use App\Filament\Resources\List\Playlist\Track;
use App\Models\Auth\User;
use App\Models\List\Playlist;
use App\Models\List\Playlist\PlaylistTrack as PlaylistTrackModel;
use Filament\Actions\Testing\TestAction;
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
            CrudPermission::VIEW->format(PlaylistTrackModel::class)
        )
        ->createOne();

    actingAs($user);

    $playlist = Playlist::factory()->tracks(3)->create();

    $records = $playlist->tracks;

    get(Track::getUrl('index'))
        ->assertSuccessful();

    Livewire::test(getIndexPage(Track::class))
        ->assertCanSeeTableRecords($records);
});

test('render view page', function () {
    $user = User::factory()
        ->withAdmin()
        ->withPermissions(
            SpecialPermission::VIEW_FILAMENT->value,
            CrudPermission::VIEW->format(PlaylistTrackModel::class)
        )
        ->createOne();

    actingAs($user);

    $playlist = Playlist::factory()->tracks(3)->create();

    $record = $playlist->tracks->first();

    get(Track::getUrl('view', ['record' => $record]))
        ->assertSuccessful();
});

test('mount create action', function () {
    $user = User::factory()
        ->withAdmin()
        ->withPermissions(
            SpecialPermission::VIEW_FILAMENT->value,
            CrudPermission::CREATE->format(PlaylistTrackModel::class)
        )
        ->createOne();

    actingAs($user);

    Livewire::test(getIndexPage(Track::class))
        ->mountAction(CreateAction::class)
        ->assertActionMounted(CreateAction::class);
});

test('mount edit action', function () {
    $user = User::factory()
        ->withAdmin()
        ->withPermissions(
            SpecialPermission::VIEW_FILAMENT->value,
            CrudPermission::UPDATE->format(PlaylistTrackModel::class),
        )
        ->createOne();

    actingAs($user);

    $playlist = Playlist::factory()->tracks(3)->create();

    $record = $playlist->tracks->first();

    Livewire::test(getIndexPage(Track::class))
        ->mountAction(TestAction::make(EditAction::getDefaultName())->table($record))
        ->callMountedAction()
        ->assertHasNoErrors();
});
