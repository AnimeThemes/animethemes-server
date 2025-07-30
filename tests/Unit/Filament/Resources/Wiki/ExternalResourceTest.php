<?php

declare(strict_types=1);

use App\Enums\Auth\CrudPermission;
use App\Enums\Auth\SpecialPermission;
use App\Filament\Actions\Base\DeleteAction;
use App\Filament\Actions\Base\EditAction;
use App\Filament\Actions\Base\ForceDeleteAction;
use App\Filament\Actions\Base\RestoreAction;
use App\Filament\Resources\Wiki\ExternalResource;
use App\Models\Auth\User;
use App\Models\Wiki\ExternalResource as ExternalResourceModel;
use Livewire\Livewire;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\get;

test('render index page', function () {
    $user = User::factory()
        ->withPermissions(
            SpecialPermission::VIEW_FILAMENT->value,
            CrudPermission::VIEW->format(ExternalResourceModel::class)
        )
        ->createOne();

    actingAs($user);

    $records = ExternalResourceModel::factory()->count(10)->create();

    get(ExternalResource::getUrl('index'))
        ->assertSuccessful();

    Livewire::test(getIndexPage(ExternalResource::class))
        ->assertCanSeeTableRecords($records);
});

test('render view page', function () {
    $user = User::factory()
        ->withPermissions(
            SpecialPermission::VIEW_FILAMENT->value,
            CrudPermission::VIEW->format(ExternalResourceModel::class)
        )
        ->createOne();

    actingAs($user);

    $record = ExternalResourceModel::factory()->createOne();

    get(ExternalResource::getUrl('view', ['record' => $record]))
        ->assertSuccessful();
});

test('mount edit action', function () {
    $user = User::factory()
        ->withPermissions(
            SpecialPermission::VIEW_FILAMENT->value,
            CrudPermission::UPDATE->format(ExternalResourceModel::class)
        )
        ->createOne();

    actingAs($user);

    $record = ExternalResourceModel::factory()->createOne();

    Livewire::test(getIndexPage(ExternalResource::class))
        ->mountAction(EditAction::class, ['record' => $record])
        ->assertActionMounted(EditAction::class);
});

test('user cannot edit record', function () {
    $record = ExternalResourceModel::factory()->createOne();

    Livewire::test(getIndexPage(ExternalResource::class))
        ->assertActionHidden(EditAction::class, ['record' => $record->getKey()]);
});

test('user cannot delete record', function () {
    $record = ExternalResourceModel::factory()->createOne();

    Livewire::test(getViewPage(ExternalResource::class), ['record' => $record->getKey()])
        ->assertActionHidden(DeleteAction::class);

    Livewire::test(getIndexPage(ExternalResource::class))
        ->assertActionHidden(DeleteAction::class, ['record' => $record->getKey()]);
});

test('user cannot restore record', function () {
    $record = ExternalResourceModel::factory()->createOne();

    $record->delete();

    Livewire::test(getViewPage(ExternalResource::class), ['record' => $record->getKey()])
        ->assertActionHidden(RestoreAction::class);

    Livewire::test(getIndexPage(ExternalResource::class))
        ->assertActionHidden(RestoreAction::class, ['record' => $record->getKey()]);
});

test('user cannot force delete record', function () {
    $record = ExternalResourceModel::factory()->createOne();

    Livewire::test(getViewPage(ExternalResource::class), ['record' => $record->getKey()])
        ->assertActionHidden(ForceDeleteAction::class);

    Livewire::test(getIndexPage(ExternalResource::class))
        ->assertActionHidden(ForceDeleteAction::class, ['record' => $record->getKey()]);
});
