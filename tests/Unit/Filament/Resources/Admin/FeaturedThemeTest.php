<?php

declare(strict_types=1);

use App\Enums\Auth\CrudPermission;
use App\Enums\Auth\SpecialPermission;
use App\Filament\Actions\Base\CreateAction;
use App\Filament\Actions\Base\DeleteAction;
use App\Filament\Actions\Base\EditAction;
use App\Filament\Resources\Admin\FeaturedTheme;
use App\Models\Admin\FeaturedTheme as FeaturedThemeModel;
use App\Models\Auth\User;
use App\Models\Wiki\Anime\Theme\AnimeThemeEntry;
use App\Models\Wiki\Video;
use Filament\Actions\Testing\TestAction;
use Livewire\Livewire;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\get;

test('render index page', function () {
    $user = User::factory()
        ->withPermissions(
            SpecialPermission::VIEW_FILAMENT->value,
            CrudPermission::VIEW->format(FeaturedThemeModel::class)
        )
        ->createOne();

    actingAs($user);

    $records = FeaturedThemeModel::factory()->count(10)->create();

    get(FeaturedTheme::getUrl('index'))
        ->assertSuccessful();

    Livewire::test(getIndexPage(FeaturedTheme::class))
        ->assertCanSeeTableRecords($records);
});

test('render view page', function () {
    $user = User::factory()
        ->withPermissions(
            SpecialPermission::VIEW_FILAMENT->value,
            CrudPermission::VIEW->format(FeaturedThemeModel::class)
        )
        ->createOne();

    actingAs($user);

    $record = FeaturedThemeModel::factory()->createOne();

    get(FeaturedTheme::getUrl('view', ['record' => $record]))
        ->assertSuccessful();
});

test('mount create action', function () {
    $user = User::factory()
        ->withPermissions(
            SpecialPermission::VIEW_FILAMENT->value,
            CrudPermission::CREATE->format(FeaturedThemeModel::class)
        )
        ->createOne();

    actingAs($user);

    Livewire::test(getIndexPage(FeaturedTheme::class))
        ->mountAction(CreateAction::class)
        ->assertActionMounted(CreateAction::class);
});

test('mount edit action', function () {
    $user = User::factory()
        ->withPermissions(
            SpecialPermission::VIEW_FILAMENT->value,
            CrudPermission::UPDATE->format(FeaturedThemeModel::class)
        )
        ->createOne();

    actingAs($user);

    $record = FeaturedThemeModel::factory()
        ->for(AnimeThemeEntry::factory()->forAnime())
        ->for(Video::factory())
        ->createOne();

    Livewire::test(getIndexPage(FeaturedTheme::class))
        ->mountAction(TestAction::make(EditAction::getDefaultName())->table($record))
        ->callMountedAction()
        ->assertHasNoErrors();
});

test('user cannot create record', function () {
    Livewire::test(getIndexPage(FeaturedTheme::class))
        ->assertActionHidden(CreateAction::class);
});

test('user cannot edit record', function () {
    $record = FeaturedThemeModel::factory()->createOne();

    Livewire::test(getIndexPage(FeaturedTheme::class))
        ->assertActionDoesNotExist(TestAction::make(EditAction::getDefaultName())->table($record));
});

test('user cannot delete record', function () {
    $record = FeaturedThemeModel::factory()->createOne();

    Livewire::test(getIndexPage(FeaturedTheme::class))
        ->assertActionDoesNotExist(TestAction::make(DeleteAction::getDefaultName())->table($record));
});
