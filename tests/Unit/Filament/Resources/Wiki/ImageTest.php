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

    get(Image::getUrl('index'))
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

    actingAs($user);

    $record = ImageModel::factory()->createOne();

    get(Image::getUrl('view', ['record' => $record]))
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

    Livewire::test(getIndexPage(Image::class))
        ->mountAction(TestAction::make(EditAction::getDefaultName())->table($record))
        ->callMountedAction()
        ->assertHasNoErrors();
});

test('user cannot edit record', function () {
    $record = ImageModel::factory()->createOne();

    Livewire::test(getIndexPage(Image::class))
        ->assertActionHidden(TestAction::make(EditAction::getDefaultName())->table($record));
});

test('user cannot delete record', function () {
    $record = ImageModel::factory()->createOne();

    Livewire::test(getIndexPage(Image::class))
        ->assertActionHidden(TestAction::make(DeleteAction::getDefaultName())->table($record));
});

test('user cannot restore record', function () {
    $record = ImageModel::factory()->createOne();

    $record->delete();

    Livewire::test(getIndexPage(Image::class))
        ->filterTable('trashed', 0)
        ->assertActionHidden(TestAction::make(RestoreAction::getDefaultName())->table($record));
});

test('user cannot force delete record', function () {
    $record = ImageModel::factory()->createOne();

    Livewire::test(getIndexPage(Image::class))
        ->assertActionHidden(TestAction::make(ForceDeleteAction::getDefaultName())->table($record));
});
