<?php

declare(strict_types=1);

use App\Enums\Auth\CrudPermission;
use App\Enums\Auth\SpecialPermission;
use App\Filament\Actions\Base\CreateAction;
use App\Filament\Actions\Base\DeleteAction;
use App\Filament\Actions\Base\EditAction;
use App\Filament\Actions\Base\ForceDeleteAction;
use App\Filament\Actions\Base\RestoreAction;
use App\Filament\Resources\Wiki\Anime\Theme\Entry;
use App\Models\Auth\User;
use App\Models\Wiki\Anime\Theme\AnimeThemeEntry as AnimeThemeEntryModel;
use Livewire\Livewire;

test('render index page', function () {
    $user = User::factory()
        ->withPermissions(
            SpecialPermission::VIEW_FILAMENT->value,
            CrudPermission::VIEW->format(AnimeThemeEntryModel::class)
        )
        ->createOne();

    $this->actingAs($user);

    $records = AnimeThemeEntryModel::factory()
        ->forAnime()
        ->count(10)->create();

    $this->get(Entry::getUrl('index'))
        ->assertSuccessful();

    Livewire::test(getIndexPage(Entry::class))
        ->assertCanSeeTableRecords($records);
});

test('render view page', function () {
    $user = User::factory()
        ->withPermissions(
            SpecialPermission::VIEW_FILAMENT->value,
            CrudPermission::VIEW->format(AnimeThemeEntryModel::class)
        )
        ->createOne();

    $this->actingAs($user);

    $record = AnimeThemeEntryModel::factory()
        ->forAnime()
        ->createOne();

    $this->get(Entry::getUrl('view', ['record' => $record]))
        ->assertSuccessful();
});

test('mount create action', function () {
    $user = User::factory()
        ->withPermissions(
            SpecialPermission::VIEW_FILAMENT->value,
            CrudPermission::CREATE->format(AnimeThemeEntryModel::class)
        )
        ->createOne();

    $this->actingAs($user);

    Livewire::test(getIndexPage(Entry::class))
        ->mountAction(CreateAction::class)
        ->assertActionMounted(CreateAction::class);
});

test('mount edit action', function () {
    $user = User::factory()
        ->withPermissions(
            SpecialPermission::VIEW_FILAMENT->value,
            CrudPermission::UPDATE->format(AnimeThemeEntryModel::class)
        )
        ->createOne();

    $this->actingAs($user);

    $record = AnimeThemeEntryModel::factory()
        ->forAnime()
        ->createOne();

    Livewire::test(getIndexPage(Entry::class))
        ->mountAction(EditAction::class, ['record' => $record])
        ->assertActionMounted(EditAction::class);
});

test('user cannot create record', function () {
    Livewire::test(getIndexPage(Entry::class))
        ->assertActionHidden(CreateAction::class);
});

test('user cannot edit record', function () {
    $record = AnimeThemeEntryModel::factory()
        ->forAnime()
        ->createOne();

    Livewire::test(getIndexPage(Entry::class))
        ->assertActionHidden(EditAction::class, ['record' => $record->getKey()]);
});

test('user cannot delete record', function () {
    $record = AnimeThemeEntryModel::factory()
        ->forAnime()
        ->createOne();

    Livewire::test(getViewPage(Entry::class), ['record' => $record->getKey()])
        ->assertActionHidden(DeleteAction::class);

    Livewire::test(getIndexPage(Entry::class))
        ->assertActionHidden(DeleteAction::class, ['record' => $record->getKey()]);
});

test('user cannot restore record', function () {
    $record = AnimeThemeEntryModel::factory()
        ->forAnime()
        ->createOne();

    $record->delete();

    Livewire::test(getViewPage(Entry::class), ['record' => $record->getKey()])
        ->assertActionHidden(RestoreAction::class);

    Livewire::test(getIndexPage(Entry::class))
        ->assertActionHidden(RestoreAction::class, ['record' => $record->getKey()]);
});

test('user cannot force delete record', function () {
    $record = AnimeThemeEntryModel::factory()
        ->forAnime()
        ->createOne();

    Livewire::test(getViewPage(Entry::class), ['record' => $record->getKey()])
        ->assertActionHidden(ForceDeleteAction::class);

    Livewire::test(getIndexPage(Entry::class))
        ->assertActionHidden(ForceDeleteAction::class, ['record' => $record->getKey()]);
});
