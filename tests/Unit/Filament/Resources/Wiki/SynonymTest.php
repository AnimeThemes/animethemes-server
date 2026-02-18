<?php

declare(strict_types=1);

use App\Enums\Auth\CrudPermission;
use App\Enums\Auth\SpecialPermission;
use App\Filament\Actions\Base\CreateAction;
use App\Filament\Actions\Base\DeleteAction;
use App\Filament\Actions\Base\EditAction;
use App\Filament\Actions\Base\ForceDeleteAction;
use App\Filament\Actions\Base\RestoreAction;
use App\Filament\Resources\Wiki\SynonymResource;
use App\Models\Auth\User;
use App\Models\Wiki\Anime;
use App\Models\Wiki\Synonym;
use Filament\Actions\Testing\TestAction;
use Livewire\Livewire;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\get;

test('render index page', function () {
    $user = User::factory()
        ->withPermissions(
            SpecialPermission::VIEW_FILAMENT->value,
            CrudPermission::VIEW->format(Synonym::class)
        )
        ->createOne();

    actingAs($user);

    $records = Synonym::factory()
        ->for(Anime::factory(), Synonym::RELATION_SYNONYMABLE)
        ->count(10)->create();

    get(SynonymResource::getUrl('index'))
        ->assertSuccessful();

    Livewire::test(getIndexPage(SynonymResource::class))
        ->assertCanSeeTableRecords($records);
});

test('render view page', function () {
    $user = User::factory()
        ->withPermissions(
            SpecialPermission::VIEW_FILAMENT->value,
            CrudPermission::VIEW->format(Synonym::class)
        )
        ->createOne();

    actingAs($user);

    $record = Synonym::factory()
        ->for(Anime::factory(), Synonym::RELATION_SYNONYMABLE)
        ->createOne();

    get(SynonymResource::getUrl('view', ['record' => $record]))
        ->assertSuccessful();
});

test('mount create action', function () {
    $user = User::factory()
        ->withPermissions(
            SpecialPermission::VIEW_FILAMENT->value,
            CrudPermission::CREATE->format(Synonym::class)
        )
        ->createOne();

    actingAs($user);

    Livewire::test(getIndexPage(SynonymResource::class))
        ->mountAction(CreateAction::class)
        ->assertActionMounted(CreateAction::class);
});

test('mount edit action', function () {
    $user = User::factory()
        ->withPermissions(
            SpecialPermission::VIEW_FILAMENT->value,
            CrudPermission::UPDATE->format(Synonym::class)
        )
        ->createOne();

    actingAs($user);

    $record = Synonym::factory()
        ->for(Anime::factory(), Synonym::RELATION_SYNONYMABLE)
        ->createOne();

    Livewire::test(getIndexPage(SynonymResource::class))
        ->mountAction(TestAction::make(EditAction::getDefaultName())->table($record))
        ->callMountedAction()
        ->assertHasNoErrors();
});

test('user cannot create record', function () {
    Livewire::test(getIndexPage(SynonymResource::class))
        ->assertActionHidden(CreateAction::class);
});

test('user cannot edit record', function () {
    $record = Synonym::factory()
        ->for(Anime::factory(), Synonym::RELATION_SYNONYMABLE)
        ->createOne();

    Livewire::test(getIndexPage(SynonymResource::class))
        ->assertActionDoesNotExist(TestAction::make(EditAction::getDefaultName())->table($record));
});

test('user cannot delete record', function () {
    $record = Synonym::factory()
        ->for(Anime::factory(), Synonym::RELATION_SYNONYMABLE)
        ->createOne();

    Livewire::test(getIndexPage(SynonymResource::class))
        ->assertActionDoesNotExist(TestAction::make(DeleteAction::getDefaultName())->table($record));
});

test('user cannot restore record', function () {
    $record = Synonym::factory()
        ->for(Anime::factory(), Synonym::RELATION_SYNONYMABLE)
        ->createOne();

    $record->delete();

    Livewire::test(getIndexPage(SynonymResource::class))
        ->filterTable('trashed', 0)
        ->assertActionDoesNotExist(TestAction::make(RestoreAction::getDefaultName())->table($record));
});

test('user cannot force delete record', function () {
    $record = Synonym::factory()
        ->for(Anime::factory(), Synonym::RELATION_SYNONYMABLE)
        ->createOne();

    Livewire::test(getIndexPage(SynonymResource::class))
        ->assertActionDoesNotExist(TestAction::make(ForceDeleteAction::getDefaultName())->table($record));
});
