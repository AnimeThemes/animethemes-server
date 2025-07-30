<?php

declare(strict_types=1);

use App\Enums\Auth\CrudPermission;
use App\Enums\Auth\SpecialPermission;
use App\Filament\Actions\Base\DeleteAction;
use App\Filament\Actions\Base\EditAction;
use App\Filament\Actions\Base\ForceDeleteAction;
use App\Filament\Actions\Base\RestoreAction;
use App\Filament\Resources\Wiki\Image;
use App\Models\Auth\User;
use App\Models\Wiki\Image as ImageModel;
use Livewire\Livewire;

test('render index page', function () {
    $user = User::factory()
        ->withPermissions(
            SpecialPermission::VIEW_FILAMENT->value,
            CrudPermission::VIEW->format(ImageModel::class)
        )
        ->createOne();

    $this->actingAs($user);

    $records = ImageModel::factory()->count(10)->create();

    $this->get(Image::getUrl('index'))
        ->assertSuccessful();

    Livewire::test(getIndexPage(Image::class))
        ->assertCanSeeTableRecords($records);
});

test('render view page', function () {
    $user = User::factory()
        ->withPermissions(
            SpecialPermission::VIEW_FILAMENT->value,
            CrudPermission::VIEW->format(ImageModel::class)
        )
        ->createOne();

    $this->actingAs($user);

    $record = ImageModel::factory()->createOne();

    $this->get(Image::getUrl('view', ['record' => $record]))
        ->assertSuccessful();
});

test('mount edit action', function () {
    $user = User::factory()
        ->withPermissions(
            SpecialPermission::VIEW_FILAMENT->value,
            CrudPermission::UPDATE->format(ImageModel::class)
        )
        ->createOne();

    $this->actingAs($user);

    $record = ImageModel::factory()->createOne();

    Livewire::test(getIndexPage(Image::class))
        ->mountAction(EditAction::class, ['record' => $record])
        ->assertActionMounted(EditAction::class);
});

test('user cannot edit record', function () {
    $record = ImageModel::factory()->createOne();

    Livewire::test(getIndexPage(Image::class))
        ->assertActionHidden(EditAction::class, ['record' => $record->getKey()]);
});

test('user cannot delete record', function () {
    $record = ImageModel::factory()->createOne();

    Livewire::test(getViewPage(Image::class), ['record' => $record->getKey()])
        ->assertActionHidden(DeleteAction::class);

    Livewire::test(getIndexPage(Image::class))
        ->assertActionHidden(DeleteAction::class, ['record' => $record->getKey()]);
});

test('user cannot restore record', function () {
    $record = ImageModel::factory()->createOne();

    $record->delete();

    Livewire::test(getViewPage(Image::class), ['record' => $record->getKey()])
        ->assertActionHidden(RestoreAction::class);

    Livewire::test(getIndexPage(Image::class))
        ->assertActionHidden(RestoreAction::class, ['record' => $record->getKey()]);
});

test('user cannot force delete record', function () {
    $record = ImageModel::factory()->createOne();

    Livewire::test(getViewPage(Image::class), ['record' => $record->getKey()])
        ->assertActionHidden(ForceDeleteAction::class);

    Livewire::test(getIndexPage(Image::class))
        ->assertActionHidden(ForceDeleteAction::class, ['record' => $record->getKey()]);
});
