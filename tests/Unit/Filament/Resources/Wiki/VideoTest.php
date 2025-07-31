<?php

declare(strict_types=1);

use App\Enums\Auth\CrudPermission;
use App\Enums\Auth\SpecialPermission;
use App\Filament\Actions\Base\DeleteAction;
use App\Filament\Actions\Base\EditAction;
use App\Filament\Actions\Base\ForceDeleteAction;
use App\Filament\Actions\Base\RestoreAction;
use App\Filament\Resources\Wiki\Video;
use App\Models\Auth\User;
use App\Models\Wiki\Video as VideoModel;
use Filament\Actions\Testing\TestAction;
use Livewire\Livewire;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\get;

test('render index page', function () {
    $user = User::factory()
        ->withPermissions(
            SpecialPermission::VIEW_FILAMENT->value,
            CrudPermission::VIEW->format(VideoModel::class)
        )
        ->createOne();

    actingAs($user);

    $records = VideoModel::factory()->count(10)->create();

    get(Video::getUrl('index'))
        ->assertSuccessful();

    Livewire::test(getIndexPage(Video::class))
        ->assertCanSeeTableRecords($records);
});

test('render view page', function () {
    $user = User::factory()
        ->withPermissions(
            SpecialPermission::VIEW_FILAMENT->value,
            CrudPermission::VIEW->format(VideoModel::class)
        )
        ->createOne();

    actingAs($user);

    $record = VideoModel::factory()->createOne();

    get(Video::getUrl('view', ['record' => $record]))
        ->assertSuccessful();
});

test('mount edit action', function () {
    $user = User::factory()
        ->withPermissions(
            SpecialPermission::VIEW_FILAMENT->value,
            CrudPermission::UPDATE->format(VideoModel::class)
        )
        ->createOne();

    actingAs($user);

    $record = VideoModel::factory()->createOne();

    Livewire::test(getIndexPage(Video::class))
        ->mountAction(TestAction::make(EditAction::getDefaultName())->table($record))
        ->callMountedAction()
        ->assertHasNoErrors();
});

test('user cannot edit record', function () {
    $record = VideoModel::factory()->createOne();

    Livewire::test(getIndexPage(Video::class))
        ->assertActionDoesNotExist(TestAction::make(EditAction::getDefaultName())->table($record));
});

test('user cannot delete record', function () {
    $record = VideoModel::factory()->createOne();

    Livewire::test(getIndexPage(Video::class))
        ->assertActionDoesNotExist(TestAction::make(DeleteAction::getDefaultName())->table($record));
});

test('user cannot restore record', function () {
    $record = VideoModel::factory()->createOne();

    $record->delete();

    Livewire::test(getIndexPage(Video::class))
        ->filterTable('trashed', 0)
        ->assertActionDoesNotExist(TestAction::make(RestoreAction::getDefaultName())->table($record));
});

test('user cannot force delete record', function () {
    $record = VideoModel::factory()->createOne();

    Livewire::test(getIndexPage(Video::class))
        ->assertActionDoesNotExist(TestAction::make(ForceDeleteAction::getDefaultName())->table($record));
});
