<?php

declare(strict_types=1);

use App\Enums\Auth\CrudPermission;
use App\Enums\Auth\SpecialPermission;
use App\Filament\Actions\Base\DeleteAction;
use App\Filament\Actions\Base\EditAction;
use App\Filament\Actions\Base\ForceDeleteAction;
use App\Filament\Actions\Base\RestoreAction;
use App\Filament\Resources\Wiki\ImageResource;
use App\Models\Auth\User;
use App\Models\Wiki\Image as ImageModel;
use Filament\Actions\Testing\TestAction;
use Livewire\Livewire;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\get;

test('render index page', function () {
    $user = User::factory()
        ->withPermissions(
            SpecialPermission::VIEW_FILAMENT->value,
            CrudPermission::VIEW->format(ImageModel::class)
        )
        ->createOne();

    actingAs($user);

    $records = ImageModel::factory()->count(10)->create();

    get(ImageResource::getUrl('index'))
        ->assertSuccessful();

    Livewire::test(getIndexPage(ImageResource::class))
        ->assertCanSeeTableRecords($records);
});

test('render view page', function () {
    $user = User::factory()
        ->withPermissions(
            SpecialPermission::VIEW_FILAMENT->value,
            CrudPermission::VIEW->format(ImageModel::class)
        )
        ->createOne();

    actingAs($user);

    $record = ImageModel::factory()->createOne();

    get(ImageResource::getUrl('view', ['record' => $record]))
        ->assertSuccessful();
});

test('mount edit action', function () {
    $user = User::factory()
        ->withPermissions(
            SpecialPermission::VIEW_FILAMENT->value,
            CrudPermission::UPDATE->format(ImageModel::class)
        )
        ->createOne();

    actingAs($user);

    $record = ImageModel::factory()->createOne();

    Livewire::test(getIndexPage(ImageResource::class))
        ->mountAction(TestAction::make(EditAction::getDefaultName())->table($record))
        ->callMountedAction()
        ->assertHasNoErrors();
});

test('user cannot edit record', function () {
    $record = ImageModel::factory()->createOne();

    Livewire::test(getIndexPage(ImageResource::class))
        ->assertActionDoesNotExist(TestAction::make(EditAction::getDefaultName())->table($record));
});

test('user cannot delete record', function () {
    $record = ImageModel::factory()->createOne();

    Livewire::test(getIndexPage(ImageResource::class))
        ->assertActionDoesNotExist(TestAction::make(DeleteAction::getDefaultName())->table($record));
});

test('user cannot restore record', function () {
    $record = ImageModel::factory()->createOne();

    $record->delete();

    Livewire::test(getIndexPage(ImageResource::class))
        ->filterTable('trashed', 0)
        ->assertActionDoesNotExist(TestAction::make(RestoreAction::getDefaultName())->table($record));
});

test('user cannot force delete record', function () {
    $record = ImageModel::factory()->createOne();

    Livewire::test(getIndexPage(ImageResource::class))
        ->assertActionDoesNotExist(TestAction::make(ForceDeleteAction::getDefaultName())->table($record));
});
