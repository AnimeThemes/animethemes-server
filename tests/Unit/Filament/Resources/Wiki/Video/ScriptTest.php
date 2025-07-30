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
use Livewire\Livewire;

test('render index page', function () {
    $user = User::factory()
        ->withPermissions(
            SpecialPermission::VIEW_FILAMENT->value,
            CrudPermission::VIEW->format(VideoScriptModel::class)
        )
        ->createOne();

    $this->actingAs($user);

    $records = VideoScriptModel::factory()->count(10)->create();

    $this->get(Script::getUrl('index'))
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

    $this->actingAs($user);

    $record = VideoScriptModel::factory()->createOne();

    $this->get(Script::getUrl('view', ['record' => $record]))
        ->assertSuccessful();
});

test('mount edit action', function () {
    $user = User::factory()
        ->withPermissions(
            SpecialPermission::VIEW_FILAMENT->value,
            CrudPermission::UPDATE->format(VideoScriptModel::class)
        )
        ->createOne();

    $this->actingAs($user);

    $record = VideoScriptModel::factory()->createOne();

    Livewire::test(getIndexPage(Script::class))
        ->mountAction(EditAction::class, ['record' => $record])
        ->assertActionMounted(EditAction::class);
});

test('user cannot edit record', function () {
    $record = VideoScriptModel::factory()->createOne();

    Livewire::test(getIndexPage(Script::class))
        ->assertActionHidden(EditAction::class, ['record' => $record->getKey()]);
});

test('user cannot delete record', function () {
    $record = VideoScriptModel::factory()->createOne();

    Livewire::test(getViewPage(Script::class), ['record' => $record->getKey()])
        ->assertActionHidden(DeleteAction::class);

    Livewire::test(getIndexPage(Script::class))
        ->assertActionHidden(DeleteAction::class, ['record' => $record->getKey()]);
});

test('user cannot restore record', function () {
    $record = VideoScriptModel::factory()->createOne();

    $record->delete();

    Livewire::test(getViewPage(Script::class), ['record' => $record->getKey()])
        ->assertActionHidden(RestoreAction::class);

    Livewire::test(getIndexPage(Script::class))
        ->assertActionHidden(RestoreAction::class, ['record' => $record->getKey()]);
});

test('user cannot force delete record', function () {
    $record = VideoScriptModel::factory()->createOne();

    Livewire::test(getViewPage(Script::class), ['record' => $record->getKey()])
        ->assertActionHidden(ForceDeleteAction::class);

    Livewire::test(getIndexPage(Script::class))
        ->assertActionHidden(ForceDeleteAction::class, ['record' => $record->getKey()]);
});
