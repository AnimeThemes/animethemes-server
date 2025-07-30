<?php

declare(strict_types=1);

use App\Enums\Auth\CrudPermission;
use App\Enums\Auth\SpecialPermission;
use App\Filament\Actions\Base\CreateAction;
use App\Filament\Actions\Base\DeleteAction;
use App\Filament\Actions\Base\EditAction;
use App\Filament\Actions\Base\ForceDeleteAction;
use App\Filament\Actions\Base\RestoreAction;
use App\Filament\Resources\Wiki\Group;
use App\Models\Auth\User;
use App\Models\Wiki\Group as GroupModel;
use Livewire\Livewire;

test('render index page', function () {
    $user = User::factory()
        ->withPermissions(
            SpecialPermission::VIEW_FILAMENT->value,
            CrudPermission::VIEW->format(GroupModel::class)
        )
        ->createOne();

    $this->actingAs($user);

    $records = GroupModel::factory()->count(10)->create();

    $this->get(Group::getUrl('index'))
        ->assertSuccessful();

    Livewire::test(getIndexPage(Group::class))
        ->assertCanSeeTableRecords($records);
});

test('render view page', function () {
    $user = User::factory()
        ->withPermissions(
            SpecialPermission::VIEW_FILAMENT->value,
            CrudPermission::VIEW->format(GroupModel::class)
        )
        ->createOne();

    $this->actingAs($user);

    $record = GroupModel::factory()->createOne();

    $this->get(Group::getUrl('view', ['record' => $record]))
        ->assertSuccessful();
});

test('mount create action', function () {
    $user = User::factory()
        ->withPermissions(
            SpecialPermission::VIEW_FILAMENT->value,
            CrudPermission::CREATE->format(GroupModel::class)
        )
        ->createOne();

    $this->actingAs($user);

    Livewire::test(getIndexPage(Group::class))
        ->mountAction(CreateAction::class)
        ->assertActionMounted(CreateAction::class);
});

test('mount edit action', function () {
    $user = User::factory()
        ->withPermissions(
            SpecialPermission::VIEW_FILAMENT->value,
            CrudPermission::UPDATE->format(GroupModel::class)
        )
        ->createOne();

    $this->actingAs($user);

    $record = GroupModel::factory()->createOne();

    Livewire::test(getIndexPage(Group::class))
        ->mountAction(EditAction::class, ['record' => $record])
        ->assertActionMounted(EditAction::class);
});

test('user cannot create record', function () {
    Livewire::test(getIndexPage(Group::class))
        ->assertActionHidden(CreateAction::class);
});

test('user cannot edit record', function () {
    $record = GroupModel::factory()->createOne();

    Livewire::test(getIndexPage(Group::class))
        ->assertActionHidden(EditAction::class, ['record' => $record->getKey()]);
});

test('user cannot delete record', function () {
    $record = GroupModel::factory()->createOne();

    Livewire::test(getViewPage(Group::class), ['record' => $record->getKey()])
        ->assertActionHidden(DeleteAction::class);

    Livewire::test(getIndexPage(Group::class))
        ->assertActionHidden(DeleteAction::class, ['record' => $record->getKey()]);
});

test('user cannot restore record', function () {
    $record = GroupModel::factory()->createOne();

    $record->delete();

    Livewire::test(getViewPage(Group::class), ['record' => $record->getKey()])
        ->assertActionHidden(RestoreAction::class);

    Livewire::test(getIndexPage(Group::class))
        ->assertActionHidden(RestoreAction::class, ['record' => $record->getKey()]);
});

test('user cannot force delete record', function () {
    $record = GroupModel::factory()->createOne();

    Livewire::test(getViewPage(Group::class), ['record' => $record->getKey()])
        ->assertActionHidden(ForceDeleteAction::class);

    Livewire::test(getIndexPage(Group::class))
        ->assertActionHidden(ForceDeleteAction::class, ['record' => $record->getKey()]);
});
