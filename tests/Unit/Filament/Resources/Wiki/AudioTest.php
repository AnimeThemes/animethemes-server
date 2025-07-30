<?php

declare(strict_types=1);

use App\Enums\Auth\CrudPermission;
use App\Enums\Auth\SpecialPermission;
use App\Filament\Actions\Base\DeleteAction;
use App\Filament\Actions\Base\EditAction;
use App\Filament\Actions\Base\ForceDeleteAction;
use App\Filament\Actions\Base\RestoreAction;
use App\Filament\Resources\Wiki\Audio;
use App\Models\Auth\User;
use App\Models\Wiki\Audio as AudioModel;
use Livewire\Livewire;

test('render index page', function () {
    $user = User::factory()
        ->withPermissions(
            SpecialPermission::VIEW_FILAMENT->value,
            CrudPermission::VIEW->format(AudioModel::class)
        )
        ->createOne();

    $this->actingAs($user);

    $records = AudioModel::factory()->count(10)->create();

    $this->get(Audio::getUrl('index'))
        ->assertSuccessful();

    Livewire::test(getIndexPage(Audio::class))
        ->assertCanSeeTableRecords($records);
});

test('render view page', function () {
    $user = User::factory()
        ->withPermissions(
            SpecialPermission::VIEW_FILAMENT->value,
            CrudPermission::VIEW->format(AudioModel::class)
        )
        ->createOne();

    $this->actingAs($user);

    $record = AudioModel::factory()->createOne();

    $this->get(Audio::getUrl('view', ['record' => $record]))
        ->assertSuccessful();
});

test('mount edit action', function () {
    $user = User::factory()
        ->withPermissions(
            SpecialPermission::VIEW_FILAMENT->value,
            CrudPermission::UPDATE->format(AudioModel::class)
        )
        ->createOne();

    $this->actingAs($user);

    $record = AudioModel::factory()->createOne();

    Livewire::test(getIndexPage(Audio::class))
        ->mountAction(EditAction::class, ['record' => $record])
        ->assertActionMounted(EditAction::class);
});

test('user cannot edit record', function () {
    $record = AudioModel::factory()->createOne();

    Livewire::test(getIndexPage(Audio::class))
        ->assertActionHidden(EditAction::class, ['record' => $record->getKey()]);
});

test('user cannot delete record', function () {
    $record = AudioModel::factory()->createOne();

    Livewire::test(getViewPage(Audio::class), ['record' => $record->getKey()])
        ->assertActionHidden(DeleteAction::class);

    Livewire::test(getIndexPage(Audio::class))
        ->assertActionHidden(DeleteAction::class, ['record' => $record->getKey()]);
});

test('user cannot restore record', function () {
    $record = AudioModel::factory()->createOne();

    $record->delete();

    Livewire::test(getViewPage(Audio::class), ['record' => $record->getKey()])
        ->assertActionHidden(RestoreAction::class);

    Livewire::test(getIndexPage(Audio::class))
        ->assertActionHidden(RestoreAction::class, ['record' => $record->getKey()]);
});

test('user cannot force delete record', function () {
    $record = AudioModel::factory()->createOne();

    Livewire::test(getViewPage(Audio::class), ['record' => $record->getKey()])
        ->assertActionHidden(ForceDeleteAction::class);

    Livewire::test(getIndexPage(Audio::class))
        ->assertActionHidden(ForceDeleteAction::class, ['record' => $record->getKey()]);
});
