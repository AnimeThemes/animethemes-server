<?php

declare(strict_types=1);

use App\Enums\Auth\CrudPermission;
use App\Enums\Auth\SpecialPermission;
use App\Filament\Actions\Base\CreateAction;
use App\Filament\Actions\Base\DeleteAction;
use App\Filament\Actions\Base\EditAction;
use App\Filament\Actions\Base\ForceDeleteAction;
use App\Filament\Actions\Base\RestoreAction;
use App\Filament\Resources\Document\Page;
use App\Models\Auth\User;
use App\Models\Document\Page as PageModel;
use Livewire\Livewire;

test('render index page', function () {
    $user = User::factory()
        ->withPermissions(
            SpecialPermission::VIEW_FILAMENT->value,
            CrudPermission::VIEW->format(PageModel::class)
        )
        ->createOne();

    $this->actingAs($user);

    $records = PageModel::factory()->count(10)->create();

    $this->get(Page::getUrl('index'))
        ->assertSuccessful();

    Livewire::test(getIndexPage(Page::class))
        ->assertCanSeeTableRecords($records);
});

test('render view page', function () {
    $user = User::factory()
        ->withPermissions(
            SpecialPermission::VIEW_FILAMENT->value,
            CrudPermission::VIEW->format(PageModel::class)
        )
        ->createOne();

    $this->actingAs($user);

    $record = PageModel::factory()->createOne();

    $this->get(Page::getUrl('view', ['record' => $record]))
        ->assertSuccessful();
});

test('mount create action', function () {
    $user = User::factory()
        ->withPermissions(
            SpecialPermission::VIEW_FILAMENT->value,
            CrudPermission::CREATE->format(PageModel::class)
        )
        ->createOne();

    $this->actingAs($user);

    Livewire::test(getIndexPage(Page::class))
        ->mountAction(CreateAction::class)
        ->assertActionMounted(CreateAction::class);
});

test('mount edit action', function () {
    $user = User::factory()
        ->withPermissions(
            SpecialPermission::VIEW_FILAMENT->value,
            CrudPermission::UPDATE->format(PageModel::class)
        )
        ->createOne();

    $this->actingAs($user);

    $record = PageModel::factory()->createOne();

    Livewire::test(getIndexPage(Page::class))
        ->mountAction(EditAction::class, ['record' => $record])
        ->assertActionMounted(EditAction::class);
});

test('user cannot create record', function () {
    Livewire::test(getIndexPage(Page::class))
        ->assertActionHidden(CreateAction::class);
});

test('user cannot edit record', function () {
    $record = PageModel::factory()->createOne();

    Livewire::test(getIndexPage(Page::class))
        ->assertActionHidden(EditAction::class, ['record' => $record->getKey()]);
});

test('user cannot delete record', function () {
    $record = PageModel::factory()->createOne();

    Livewire::test(getViewPage(Page::class), ['record' => $record->getKey()])
        ->assertActionHidden(DeleteAction::class);

    Livewire::test(getIndexPage(Page::class))
        ->assertActionHidden(DeleteAction::class, ['record' => $record->getKey()]);
});

test('user cannot restore record', function () {
    $record = PageModel::factory()->createOne();

    $record->delete();

    Livewire::test(getViewPage(Page::class), ['record' => $record->getKey()])
        ->assertActionHidden(RestoreAction::class);

    Livewire::test(getIndexPage(Page::class))
        ->assertActionHidden(RestoreAction::class, ['record' => $record->getKey()]);
});

test('user cannot force delete record', function () {
    $record = PageModel::factory()->createOne();

    Livewire::test(getViewPage(Page::class), ['record' => $record->getKey()])
        ->assertActionHidden(ForceDeleteAction::class);

    Livewire::test(getIndexPage(Page::class))
        ->assertActionHidden(ForceDeleteAction::class, ['record' => $record->getKey()]);
});
