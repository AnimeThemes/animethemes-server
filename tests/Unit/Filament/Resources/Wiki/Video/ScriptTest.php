<?php

declare(strict_types=1);

use App\Enums\Auth\CrudPermission;
use App\Enums\Auth\SpecialPermission;
use App\Filament\Actions\Base\DeleteAction;
use App\Filament\Actions\Base\EditAction;
use App\Filament\Actions\Base\ForceDeleteAction;
use App\Filament\Actions\Base\RestoreAction;
use App\Filament\Resources\Wiki\Video\Script;
use App\Models\Auth\User;
use App\Models\Wiki\Video\VideoScript as VideoScriptModel;
use Filament\Actions\Testing\TestAction;
use Livewire\Livewire;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\get;

test('render index page', function () {
    $user = User::factory()
        ->withPermissions(
            SpecialPermission::VIEW_FILAMENT->value,
            CrudPermission::VIEW->format(VideoScriptModel::class)
        )
        ->createOne();

    actingAs($user);

    $records = VideoScriptModel::factory()->count(10)->create();

    get(Script::getUrl('index'))
        ->assertSuccessful();

    Livewire::test(getIndexPage(Script::class))
        ->assertCanSeeTableRecords($records);
});

test('render view page', function () {
    $user = User::factory()
        ->withPermissions(
            SpecialPermission::VIEW_FILAMENT->value,
            CrudPermission::VIEW->format(VideoScriptModel::class)
        )
        ->createOne();

    actingAs($user);

    $record = VideoScriptModel::factory()->createOne();

    get(Script::getUrl('view', ['record' => $record]))
        ->assertSuccessful();
});

test('mount edit action', function () {
    $user = User::factory()
        ->withPermissions(
            SpecialPermission::VIEW_FILAMENT->value,
            CrudPermission::UPDATE->format(VideoScriptModel::class)
        )
        ->createOne();

    actingAs($user);

    $record = VideoScriptModel::factory()->createOne();

    Livewire::test(getIndexPage(Script::class))
        ->mountAction(TestAction::make(EditAction::getDefaultName())->table($record))
        ->callMountedAction()
        ->assertHasNoErrors();
});

test('user cannot edit record', function () {
    $record = VideoScriptModel::factory()->createOne();

    Livewire::test(getIndexPage(Script::class))
        ->assertActionDoesNotExist(TestAction::make(EditAction::getDefaultName())->table($record));
});

test('user cannot delete record', function () {
    $record = VideoScriptModel::factory()->createOne();

    Livewire::test(getIndexPage(Script::class))
        ->assertActionDoesNotExist(TestAction::make(DeleteAction::getDefaultName())->table($record));
});

test('user cannot restore record', function () {
    $record = VideoScriptModel::factory()->createOne();

    $record->delete();

    Livewire::test(getIndexPage(Script::class))
        ->filterTable('trashed', 0)
        ->assertActionDoesNotExist(TestAction::make(RestoreAction::getDefaultName())->table($record));
});

test('user cannot force delete record', function () {
    $record = VideoScriptModel::factory()->createOne();

    Livewire::test(getIndexPage(Script::class))
        ->assertActionDoesNotExist(TestAction::make(ForceDeleteAction::getDefaultName())->table($record));
});
