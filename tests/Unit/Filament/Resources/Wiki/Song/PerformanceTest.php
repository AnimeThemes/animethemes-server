<?php

declare(strict_types=1);

use App\Enums\Auth\CrudPermission;
use App\Enums\Auth\SpecialPermission;
use App\Filament\Actions\Base\CreateAction;
use App\Filament\Actions\Base\DeleteAction;
use App\Filament\Actions\Base\EditAction;
use App\Filament\Actions\Base\ForceDeleteAction;
use App\Filament\Actions\Base\RestoreAction;
use App\Filament\Resources\Wiki\Song\Performance;
use App\Models\Auth\User;
use App\Models\Wiki\Artist;
use App\Models\Wiki\Song;
use App\Models\Wiki\Song\Performance as PerformanceModel;
use Filament\Actions\Testing\TestAction;
use Livewire\Livewire;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\get;

test('render index page', function () {
    $user = User::factory()
        ->withPermissions(
            SpecialPermission::VIEW_FILAMENT->value,
            CrudPermission::VIEW->format(PerformanceModel::class)
        )
        ->createOne();

    actingAs($user);

    $records = PerformanceModel::factory()
        ->for(Song::factory())
        ->artist(Artist::factory()->createOne())
        ->create();

    get(Performance::getUrl('index'))
        ->assertSuccessful();

    Livewire::test(getIndexPage(Performance::class))
        ->assertCanSeeTableRecords(collect([$records]));
});

test('render view page', function () {
    $user = User::factory()
        ->withPermissions(
            SpecialPermission::VIEW_FILAMENT->value,
            CrudPermission::VIEW->format(PerformanceModel::class)
        )
        ->createOne();

    actingAs($user);

    $record = PerformanceModel::factory()
        ->for(Song::factory())
        ->artist(Artist::factory()->createOne())
        ->createOne();

    get(Performance::getUrl('view', ['record' => $record]))
        ->assertSuccessful();
});

test('mount create action', function () {
    $user = User::factory()
        ->withPermissions(
            SpecialPermission::VIEW_FILAMENT->value,
            CrudPermission::CREATE->format(PerformanceModel::class)
        )
        ->createOne();

    actingAs($user);

    Livewire::test(getIndexPage(Performance::class))
        ->mountAction(CreateAction::class)
        ->assertActionMounted(CreateAction::class);
});

test('mount edit action', function () {
    $user = User::factory()
        ->withPermissions(
            SpecialPermission::VIEW_FILAMENT->value,
            CrudPermission::UPDATE->format(PerformanceModel::class)
        )
        ->createOne();

    actingAs($user);

    $record = PerformanceModel::factory()
        ->for(Song::factory())
        ->artist(Artist::factory()->createOne())
        ->createOne();

    Livewire::test(getIndexPage(Performance::class))
        ->mountAction(TestAction::make(EditAction::getDefaultName())->table($record))
        ->callMountedAction()
        ->assertHasNoErrors();
});

test('user cannot create record', function () {
    Livewire::test(getIndexPage(Performance::class))
        ->assertActionHidden(CreateAction::class);
});

test('user cannot edit record', function () {
    $record = PerformanceModel::factory()
        ->for(Song::factory())
        ->artist(Artist::factory()->createOne())
        ->createOne();

    Livewire::test(getIndexPage(Performance::class))
        ->assertActionDoesNotExist(TestAction::make(EditAction::getDefaultName())->table($record));
});

test('user cannot delete record', function () {
    $record = PerformanceModel::factory()
        ->for(Song::factory())
        ->artist(Artist::factory()->createOne())
        ->createOne();

    Livewire::test(getIndexPage(Performance::class))
        ->assertActionDoesNotExist(TestAction::make(DeleteAction::getDefaultName())->table($record));
});

test('user cannot restore record', function () {
    $record = PerformanceModel::factory()
        ->for(Song::factory())
        ->artist(Artist::factory()->createOne())
        ->createOne();

    $record->delete();

    Livewire::test(getIndexPage(Performance::class))
        ->filterTable('trashed', 0)
        ->assertActionDoesNotExist(TestAction::make(RestoreAction::getDefaultName())->table($record));
});

test('user cannot force delete record', function () {
    $record = PerformanceModel::factory()
        ->for(Song::factory())
        ->artist(Artist::factory()->createOne())
        ->createOne();

    Livewire::test(getIndexPage(Performance::class))
        ->assertActionDoesNotExist(TestAction::make(ForceDeleteAction::getDefaultName())->table($record));
});
