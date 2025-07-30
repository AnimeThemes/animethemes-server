<?php

declare(strict_types=1);

use App\Enums\Auth\CrudPermission;
use App\Enums\Auth\SpecialPermission;
use App\Filament\Actions\Base\CreateAction;
use App\Filament\Actions\Base\DeleteAction;
use App\Filament\Actions\Base\EditAction;
use App\Filament\Actions\Base\ForceDeleteAction;
use App\Filament\Actions\Base\RestoreAction;
use App\Filament\Resources\Wiki\Song\Membership;
use App\Models\Auth\User;
use App\Models\Wiki\Artist;
use App\Models\Wiki\Song\Membership as MembershipModel;
use Livewire\Livewire;

test('render index page', function () {
    $user = User::factory()
        ->withPermissions(
            SpecialPermission::VIEW_FILAMENT->value,
            CrudPermission::VIEW->format(MembershipModel::class)
        )
        ->createOne();

    $this->actingAs($user);

    $records = MembershipModel::factory()
        ->count(10)
        ->for(Artist::factory(), MembershipModel::RELATION_ARTIST)
        ->for(Artist::factory(), MembershipModel::RELATION_MEMBER)
        ->create();

    $this->get(Membership::getUrl('index'))
        ->assertSuccessful();

    Livewire::test(getIndexPage(Membership::class))
        ->assertCanSeeTableRecords($records);
});

test('render view page', function () {
    $user = User::factory()
        ->withPermissions(
            SpecialPermission::VIEW_FILAMENT->value,
            CrudPermission::VIEW->format(MembershipModel::class)
        )
        ->createOne();

    $this->actingAs($user);

    $record = MembershipModel::factory()
        ->for(Artist::factory(), MembershipModel::RELATION_ARTIST)
        ->for(Artist::factory(), MembershipModel::RELATION_MEMBER)
        ->createOne();

    $this->get(Membership::getUrl('view', ['record' => $record]))
        ->assertSuccessful();
});

test('mount create action', function () {
    $user = User::factory()
        ->withPermissions(
            SpecialPermission::VIEW_FILAMENT->value,
            CrudPermission::CREATE->format(MembershipModel::class)
        )
        ->createOne();

    $this->actingAs($user);

    Livewire::test(getIndexPage(Membership::class))
        ->mountAction(CreateAction::class)
        ->assertActionMounted(CreateAction::class);
});

test('mount edit action', function () {
    $user = User::factory()
        ->withPermissions(
            SpecialPermission::VIEW_FILAMENT->value,
            CrudPermission::UPDATE->format(MembershipModel::class)
        )
        ->createOne();

    $this->actingAs($user);

    $record = MembershipModel::factory()
        ->for(Artist::factory(), MembershipModel::RELATION_ARTIST)
        ->for(Artist::factory(), MembershipModel::RELATION_MEMBER)
        ->createOne();

    Livewire::test(getIndexPage(Membership::class))
        ->mountAction(EditAction::class, ['record' => $record])
        ->assertActionMounted(EditAction::class);
});

test('user cannot create record', function () {
    Livewire::test(getIndexPage(Membership::class))
        ->assertActionHidden(CreateAction::class);
});

test('user cannot edit record', function () {
    $record = MembershipModel::factory()
        ->for(Artist::factory(), MembershipModel::RELATION_ARTIST)
        ->for(Artist::factory(), MembershipModel::RELATION_MEMBER)
        ->createOne();

    Livewire::test(getIndexPage(Membership::class))
        ->assertActionHidden(EditAction::class, ['record' => $record->getKey()]);
});

test('user cannot delete record', function () {
    $record = MembershipModel::factory()
        ->for(Artist::factory(), MembershipModel::RELATION_ARTIST)
        ->for(Artist::factory(), MembershipModel::RELATION_MEMBER)
        ->createOne();

    Livewire::test(getViewPage(Membership::class), ['record' => $record->getKey()])
        ->assertActionHidden(DeleteAction::class);

    Livewire::test(getIndexPage(Membership::class))
        ->assertActionHidden(DeleteAction::class, ['record' => $record->getKey()]);
});

test('user cannot restore record', function () {
    $record = MembershipModel::factory()
        ->for(Artist::factory(), MembershipModel::RELATION_ARTIST)
        ->for(Artist::factory(), MembershipModel::RELATION_MEMBER)
        ->createOne();

    $record->delete();

    Livewire::test(getViewPage(Membership::class), ['record' => $record->getKey()])
        ->assertActionHidden(RestoreAction::class);

    Livewire::test(getIndexPage(Membership::class))
        ->assertActionHidden(RestoreAction::class, ['record' => $record->getKey()]);
});

test('user cannot force delete record', function () {
    $record = MembershipModel::factory()
        ->for(Artist::factory(), MembershipModel::RELATION_ARTIST)
        ->for(Artist::factory(), MembershipModel::RELATION_MEMBER)
        ->createOne();

    Livewire::test(getViewPage(Membership::class), ['record' => $record->getKey()])
        ->assertActionHidden(ForceDeleteAction::class);

    Livewire::test(getIndexPage(Membership::class))
        ->assertActionHidden(ForceDeleteAction::class, ['record' => $record->getKey()]);
});
