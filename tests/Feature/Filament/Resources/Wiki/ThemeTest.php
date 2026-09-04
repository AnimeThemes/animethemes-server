<?php

declare(strict_types=1);

use App\Enums\Auth\CrudPermission;
use App\Enums\Auth\SpecialPermission;
use App\Filament\Actions\Base\CreateAction;
use App\Filament\Actions\Base\DeleteAction;
use App\Filament\Actions\Base\EditAction;
use App\Filament\Actions\Base\ForceDeleteAction;
use App\Filament\Actions\Base\RestoreAction;
use App\Filament\Resources\Wiki\ThemeResource;
use App\Models\Auth\User;
use App\Models\Wiki\Anime;
use App\Models\Wiki\Theme as ThemeModel;
use Filament\Actions\Testing\TestAction;
use Livewire\Livewire;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\get;

test('render index page', function (): void {
    $user = User::factory()
        ->withPermissions(
            SpecialPermission::VIEW_FILAMENT->value,
            CrudPermission::VIEW->format(ThemeModel::class)
        )
        ->createOne();

    actingAs($user);

    $records = ThemeModel::factory()
        ->for(Anime::factory())
        ->count(10)->create();

    get(ThemeResource::getUrl('index'))
        ->assertSuccessful();

    Livewire::test(getIndexPage(ThemeResource::class))
        ->assertCanSeeTableRecords($records);
});

test('render view page', function (): void {
    $user = User::factory()
        ->withPermissions(
            SpecialPermission::VIEW_FILAMENT->value,
            CrudPermission::VIEW->format(ThemeModel::class)
        )
        ->createOne();

    actingAs($user);

    $record = ThemeModel::factory()
        ->for(Anime::factory())
        ->createOne();

    get(ThemeResource::getUrl('view', ['record' => $record]))
        ->assertSuccessful();
});

test('mount create action', function (): void {
    $user = User::factory()
        ->withPermissions(
            SpecialPermission::VIEW_FILAMENT->value,
            CrudPermission::CREATE->format(ThemeModel::class)
        )
        ->createOne();

    actingAs($user);

    Livewire::test(getIndexPage(ThemeResource::class))
        ->mountAction(CreateAction::class)
        ->assertActionMounted(CreateAction::class);
});

test('mount edit action', function (): void {
    $user = User::factory()
        ->withPermissions(
            SpecialPermission::VIEW_FILAMENT->value,
            CrudPermission::UPDATE->format(ThemeModel::class)
        )
        ->createOne();

    actingAs($user);

    $record = ThemeModel::factory()
        ->for(Anime::factory())
        ->createOne();

    Livewire::test(getIndexPage(ThemeResource::class))
        ->mountAction(TestAction::make(EditAction::getDefaultName())->table($record))
        ->callMountedAction()
        ->assertHasNoErrors();
});

test('user cannot create record', function (): void {
    Livewire::test(getIndexPage(ThemeResource::class))
        ->assertActionHidden(CreateAction::class);
});

test('user cannot edit record', function (): void {
    $record = ThemeModel::factory()
        ->for(Anime::factory())
        ->createOne();

    Livewire::test(getIndexPage(ThemeResource::class))
        ->assertActionDoesNotExist(TestAction::make(EditAction::getDefaultName())->table($record));
});

test('user cannot delete record', function (): void {
    $record = ThemeModel::factory()
        ->for(Anime::factory())
        ->createOne();

    Livewire::test(getIndexPage(ThemeResource::class))
        ->assertActionDoesNotExist(TestAction::make(DeleteAction::getDefaultName())->table($record));
});

test('user cannot restore record', function (): void {
    $record = ThemeModel::factory()
        ->for(Anime::factory())
        ->createOne();

    $record->delete();

    Livewire::test(getIndexPage(ThemeResource::class))
        ->filterTable('trashed', 0)
        ->assertActionDoesNotExist(TestAction::make(RestoreAction::getDefaultName())->table($record));
});

test('user cannot force delete record', function (): void {
    $record = ThemeModel::factory()
        ->for(Anime::factory())
        ->createOne();

    Livewire::test(getIndexPage(ThemeResource::class))
        ->assertActionDoesNotExist(TestAction::make(ForceDeleteAction::getDefaultName())->table($record));
});
