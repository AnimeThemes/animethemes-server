<?php

declare(strict_types=1);

use App\Enums\Auth\CrudPermission;
use App\Enums\Auth\SpecialPermission;
use App\Filament\Actions\Base\CreateAction;
use App\Filament\Actions\Base\DeleteAction;
use App\Filament\Actions\Base\EditAction;
use App\Filament\Actions\Base\ForceDeleteAction;
use App\Filament\Actions\Base\RestoreAction;
use App\Filament\Resources\Wiki\Anime\Theme;
use App\Models\Auth\User;
use App\Models\Wiki\Anime;
use App\Models\Wiki\Anime\AnimeTheme as AnimeThemeModel;
use Filament\Actions\Testing\TestAction;
use Livewire\Livewire;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\get;

test('render index page', function () {
    $user = User::factory()
        ->withPermissions(
            SpecialPermission::VIEW_FILAMENT->value,
            CrudPermission::VIEW->format(AnimeThemeModel::class)
        )
        ->createOne();

    actingAs($user);

    $records = AnimeThemeModel::factory()
        ->for(Anime::factory())
        ->count(10)->create();

    get(Theme::getUrl('index'))
        ->assertSuccessful();

    Livewire::test(getIndexPage(Theme::class))
        ->assertCanSeeTableRecords($records);
});

test('render view page', function () {
    $user = User::factory()
        ->withPermissions(
            SpecialPermission::VIEW_FILAMENT->value,
            CrudPermission::VIEW->format(AnimeThemeModel::class)
        )
        ->createOne();

    actingAs($user);

    $record = AnimeThemeModel::factory()
        ->for(Anime::factory())
        ->createOne();

    get(Theme::getUrl('view', ['record' => $record]))
        ->assertSuccessful();
});

test('mount create action', function () {
    $user = User::factory()
        ->withPermissions(
            SpecialPermission::VIEW_FILAMENT->value,
            CrudPermission::CREATE->format(AnimeThemeModel::class)
        )
        ->createOne();

    actingAs($user);

    Livewire::test(getIndexPage(Theme::class))
        ->mountAction(CreateAction::class)
        ->assertActionMounted(CreateAction::class);
});

test('mount edit action', function () {
    $user = User::factory()
        ->withPermissions(
            SpecialPermission::VIEW_FILAMENT->value,
            CrudPermission::UPDATE->format(AnimeThemeModel::class)
        )
        ->createOne();

    actingAs($user);

    $record = AnimeThemeModel::factory()
        ->for(Anime::factory())
        ->createOne();

    Livewire::test(getIndexPage(Theme::class))
        ->mountAction(TestAction::make(EditAction::getDefaultName())->table($record))
        ->callMountedAction()
        ->assertHasNoErrors();
});

test('user cannot create record', function () {
    Livewire::test(getIndexPage(Theme::class))
        ->assertActionHidden(CreateAction::class);
});

test('user cannot edit record', function () {
    $record = AnimeThemeModel::factory()
        ->for(Anime::factory())
        ->createOne();

    Livewire::test(getIndexPage(Theme::class))
        ->assertActionDoesNotExist(TestAction::make(EditAction::getDefaultName())->table($record));
});

test('user cannot delete record', function () {
    $record = AnimeThemeModel::factory()
        ->for(Anime::factory())
        ->createOne();

    Livewire::test(getIndexPage(Theme::class))
        ->assertActionDoesNotExist(TestAction::make(DeleteAction::getDefaultName())->table($record));
});

test('user cannot restore record', function () {
    $record = AnimeThemeModel::factory()
        ->for(Anime::factory())
        ->createOne();

    $record->delete();

    Livewire::test(getIndexPage(Theme::class))
        ->filterTable('trashed', 0)
        ->assertActionDoesNotExist(TestAction::make(RestoreAction::getDefaultName())->table($record));
});

test('user cannot force delete record', function () {
    $record = AnimeThemeModel::factory()
        ->for(Anime::factory())
        ->createOne();

    Livewire::test(getIndexPage(Theme::class))
        ->assertActionDoesNotExist(TestAction::make(ForceDeleteAction::getDefaultName())->table($record));
});
