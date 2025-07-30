<?php

declare(strict_types=1);

use App\Enums\Auth\CrudPermission;
use App\Enums\Auth\SpecialPermission;
use App\Filament\Actions\Base\CreateAction;
use App\Filament\Actions\Base\DeleteAction;
use App\Filament\Actions\Base\EditAction;
use App\Filament\Actions\Base\ForceDeleteAction;
use App\Filament\Actions\Base\RestoreAction;
use App\Filament\Resources\Admin\Announcement;
use App\Models\Admin\Announcement as AnnouncementModel;
use App\Models\Auth\User;
use Livewire\Livewire;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\get;

test('render index page', function () {
    $user = User::factory()
        ->withPermissions(
            SpecialPermission::VIEW_FILAMENT->value,
            CrudPermission::VIEW->format(AnnouncementModel::class)
        )
        ->createOne();

    actingAs($user);

    $records = AnnouncementModel::factory()->count(10)->create();

    get(Announcement::getUrl('index'))
        ->assertSuccessful();

    Livewire::test(getIndexPage(Announcement::class))
        ->assertCanSeeTableRecords($records);
});

test('user cannot create record', function () {
    Livewire::test(getIndexPage(Announcement::class))
        ->assertActionHidden(CreateAction::class);
});

test('user cannot edit record', function () {
    $record = AnnouncementModel::factory()->createOne();

    Livewire::test(getIndexPage(Announcement::class))
        ->assertActionHidden(EditAction::class, ['record' => $record->getKey()]);
});

test('user cannot delete record', function () {
    $record = AnnouncementModel::factory()->createOne();

    Livewire::test(getIndexPage(Announcement::class))
        ->assertActionHidden(DeleteAction::class, ['record' => $record->getKey()]);
});

test('user cannot restore record', function () {
    $record = AnnouncementModel::factory()->createOne();

    Livewire::test(getIndexPage(Announcement::class))
        ->assertActionHidden(RestoreAction::class, ['record' => $record->getKey()]);
});

test('user cannot force delete record', function () {
    $record = AnnouncementModel::factory()->createOne();

    Livewire::test(getIndexPage(Announcement::class))
        ->assertActionHidden(ForceDeleteAction::class, ['record' => $record->getKey()]);
});
