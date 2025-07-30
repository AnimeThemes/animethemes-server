<?php

declare(strict_types=1);

use App\Enums\Auth\CrudPermission;
use App\Enums\Auth\SpecialPermission;
use App\Filament\Actions\Base\CreateAction;
use App\Filament\Actions\Base\DeleteAction;
use App\Filament\Actions\Base\EditAction;
use App\Filament\Actions\Base\ForceDeleteAction;
use App\Filament\Actions\Base\RestoreAction;
use App\Filament\Resources\Admin\FeaturedTheme;
use App\Models\Admin\FeaturedTheme as FeaturedThemeModel;
use App\Models\Auth\User;
use Livewire\Livewire;

test('render index page', function () {
    $user = User::factory()
        ->withPermissions(
            SpecialPermission::VIEW_FILAMENT->value,
            CrudPermission::VIEW->format(FeaturedThemeModel::class)
        )
        ->createOne();

    $this->actingAs($user);

    $records = FeaturedThemeModel::factory()->count(10)->create();

    $this->get(FeaturedTheme::getUrl('index'))
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

    $this->actingAs($user);

    $record = FeaturedThemeModel::factory()->createOne();

    $this->get(FeaturedTheme::getUrl('view', ['record' => $record]))
        ->assertSuccessful();
});

test('mount create action', function () {
    $user = User::factory()
        ->withPermissions(
            SpecialPermission::VIEW_FILAMENT->value,
            CrudPermission::CREATE->format(FeaturedThemeModel::class)
        )
        ->createOne();

    $this->actingAs($user);

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

    $this->actingAs($user);

    $record = FeaturedThemeModel::factory()->createOne();

    Livewire::test(getIndexPage(FeaturedTheme::class))
        ->mountAction(EditAction::class, ['record' => $record])
        ->assertActionMounted(EditAction::class);
});

test('user cannot create record', function () {
    Livewire::test(getIndexPage(FeaturedTheme::class))
        ->assertActionHidden(CreateAction::class);
});

test('user cannot edit record', function () {
    $record = FeaturedThemeModel::factory()->createOne();

    Livewire::test(getIndexPage(FeaturedTheme::class))
        ->assertActionHidden(EditAction::class, ['record' => $record->getKey()]);
});

test('user cannot delete record', function () {
    $record = FeaturedThemeModel::factory()->createOne();

    Livewire::test(getViewPage(FeaturedTheme::class), ['record' => $record->getKey()])
        ->assertActionHidden(DeleteAction::class);

    Livewire::test(getIndexPage(FeaturedTheme::class))
        ->assertActionHidden(DeleteAction::class, ['record' => $record->getKey()]);
});

test('user cannot restore record', function () {
    $record = FeaturedThemeModel::factory()->createOne();

    $record->delete();

    Livewire::test(getViewPage(FeaturedTheme::class), ['record' => $record->getKey()])
        ->assertActionHidden(RestoreAction::class);

    Livewire::test(getIndexPage(FeaturedTheme::class))
        ->assertActionHidden(RestoreAction::class, ['record' => $record->getKey()]);
});

test('user cannot force delete record', function () {
    $record = FeaturedThemeModel::factory()->createOne();

    Livewire::test(getViewPage(FeaturedTheme::class), ['record' => $record->getKey()])
        ->assertActionHidden(ForceDeleteAction::class);

    Livewire::test(getIndexPage(FeaturedTheme::class))
        ->assertActionHidden(ForceDeleteAction::class, ['record' => $record->getKey()]);
});
