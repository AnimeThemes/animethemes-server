<?php

declare(strict_types=1);

use App\Enums\Auth\CrudPermission;
use App\Enums\Auth\SpecialPermission;
use App\Filament\Actions\Base\CreateAction;
use App\Filament\Actions\Base\DeleteAction;
use App\Filament\Actions\Base\EditAction;
use App\Filament\Actions\Base\ForceDeleteAction;
use App\Filament\Actions\Base\RestoreAction;
use App\Filament\Resources\Wiki\AnimeResource;
use App\Models\Auth\User;
use App\Models\Wiki\Anime as AnimeModel;
use Filament\Actions\Testing\TestAction;
use Livewire\Livewire;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\get;

test('render index page', function () {
    $user = User::factory()
        ->withPermissions(
            SpecialPermission::VIEW_FILAMENT->value,
            CrudPermission::VIEW->format(AnimeModel::class)
        )
        ->createOne();

    actingAs($user);

    $records = AnimeModel::factory()->count(10)->create();

    get(AnimeResource::getUrl('index'))
        ->assertSuccessful();

    Livewire::test(getIndexPage(AnimeResource::class))
        ->assertCanSeeTableRecords($records);
});

test('render view page', function () {
    $user = User::factory()
        ->withPermissions(
            SpecialPermission::VIEW_FILAMENT->value,
            CrudPermission::VIEW->format(AnimeModel::class)
        )
        ->createOne();

    actingAs($user);

    $record = AnimeModel::factory()->createOne();

    get(AnimeResource::getUrl('view', ['record' => $record]))
        ->assertSuccessful();
});

test('mount create action', function () {
    $user = User::factory()
        ->withPermissions(
            SpecialPermission::VIEW_FILAMENT->value,
            CrudPermission::CREATE->format(AnimeModel::class)
        )
        ->createOne();

    actingAs($user);

    Livewire::test(getIndexPage(AnimeResource::class))
        ->mountAction(CreateAction::class)
        ->assertActionMounted(CreateAction::class);
});

test('mount edit action', function () {
    $user = User::factory()
        ->withPermissions(
            SpecialPermission::VIEW_FILAMENT->value,
            CrudPermission::UPDATE->format(AnimeModel::class)
        )
        ->createOne();

    actingAs($user);

    $record = AnimeModel::factory()->createOne();

    Livewire::test(getIndexPage(AnimeResource::class))
        ->mountAction(TestAction::make(EditAction::getDefaultName())->table($record))
        ->callMountedAction()
        ->assertHasNoErrors();
});

test('user cannot create record', function () {
    Livewire::test(getIndexPage(AnimeResource::class))
        ->assertActionHidden(CreateAction::class);
});

test('user cannot edit record', function () {
    $record = AnimeModel::factory()->createOne();

    Livewire::test(getIndexPage(AnimeResource::class))
        ->assertActionDoesNotExist(TestAction::make(EditAction::getDefaultName())->table($record));
});

test('user cannot delete record', function () {
    $record = AnimeModel::factory()->createOne();

    Livewire::test(getIndexPage(AnimeResource::class))
        ->assertActionDoesNotExist(TestAction::make(DeleteAction::getDefaultName())->table($record));
});

test('user cannot restore record', function () {
    $record = AnimeModel::factory()->createOne();

    $record->delete();

    Livewire::test(getIndexPage(AnimeResource::class))
        ->filterTable('trashed', 0)
        ->assertActionDoesNotExist(TestAction::make(RestoreAction::getDefaultName())->table($record));
});

test('user cannot force delete record', function () {
    $record = AnimeModel::factory()->createOne();

    Livewire::test(getIndexPage(AnimeResource::class))
        ->assertActionDoesNotExist(TestAction::make(ForceDeleteAction::getDefaultName())->table($record));
});
