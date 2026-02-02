<?php

declare(strict_types=1);

use App\Enums\Auth\CrudPermission;
use App\Enums\Auth\SpecialPermission;
use App\Filament\Actions\Base\CreateAction;
use App\Filament\Actions\Base\DeleteAction;
use App\Filament\Actions\Base\EditAction;
use App\Filament\Resources\Discord\DiscordThreadResource;
use App\Models\Auth\User;
use App\Models\Discord\DiscordThread as DiscordThreadModel;
use App\Models\Wiki\Anime;
use Filament\Actions\Testing\TestAction;
use Livewire\Livewire;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\get;

test('render index page', function () {
    $user = User::factory()
        ->withPermissions(
            SpecialPermission::VIEW_FILAMENT->value,
            CrudPermission::VIEW->format(DiscordThreadModel::class)
        )
        ->createOne();

    actingAs($user);

    $records = DiscordThreadModel::factory()
        ->for(Anime::factory())
        ->count(10)
        ->create();

    get(DiscordThreadResource::getUrl('index'))
        ->assertSuccessful();

    Livewire::test(getIndexPage(DiscordThreadResource::class))
        ->assertCanSeeTableRecords($records);
});

test('render view page', function () {
    $user = User::factory()
        ->withPermissions(
            SpecialPermission::VIEW_FILAMENT->value,
            CrudPermission::VIEW->format(DiscordThreadModel::class)
        )
        ->createOne();

    actingAs($user);

    $record = DiscordThreadModel::factory()
        ->for(Anime::factory())
        ->createOne();

    get(DiscordThreadResource::getUrl('view', ['record' => $record]))
        ->assertSuccessful();
});

test('mount create action', function () {
    $user = User::factory()
        ->withPermissions(
            SpecialPermission::VIEW_FILAMENT->value,
            CrudPermission::CREATE->format(DiscordThreadModel::class)
        )
        ->createOne();

    actingAs($user);

    Livewire::test(getIndexPage(DiscordThreadResource::class))
        ->mountAction(CreateAction::class)
        ->assertActionMounted(CreateAction::class);
});

test('mount edit action', function () {
    $user = User::factory()
        ->withPermissions(
            SpecialPermission::VIEW_FILAMENT->value,
            CrudPermission::UPDATE->format(DiscordThreadModel::class)
        )
        ->createOne();

    actingAs($user);

    $record = DiscordThreadModel::factory()
        ->for(Anime::factory())
        ->createOne();

    Livewire::test(getIndexPage(DiscordThreadResource::class))
        ->mountAction(TestAction::make(EditAction::getDefaultName())->table($record))
        ->callMountedAction()
        ->assertHasNoErrors();
});

test('user cannot create record', function () {
    Livewire::test(getIndexPage(DiscordThreadResource::class))
        ->assertActionHidden(CreateAction::class);
});

test('user cannot edit record', function () {
    $record = DiscordThreadModel::factory()
        ->for(Anime::factory())
        ->createOne();

    Livewire::test(getIndexPage(DiscordThreadResource::class))
        ->assertActionDoesNotExist(TestAction::make(EditAction::getDefaultName())->table($record));
});

test('user cannot delete record', function () {
    $record = DiscordThreadModel::factory()
        ->for(Anime::factory())
        ->createOne();

    Livewire::test(getIndexPage(DiscordThreadResource::class))
        ->assertActionDoesNotExist(TestAction::make(DeleteAction::getDefaultName())->table($record));
});
