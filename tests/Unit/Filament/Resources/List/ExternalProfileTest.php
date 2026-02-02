<?php

declare(strict_types=1);

use App\Enums\Auth\CrudPermission;
use App\Enums\Auth\SpecialPermission;
use App\Filament\Actions\Base\CreateAction;
use App\Filament\Actions\Base\EditAction;
use App\Filament\Resources\List\ExternalProfileResource;
use App\Models\Auth\User;
use App\Models\List\ExternalProfile as ExternalProfileModel;
use Filament\Actions\Testing\TestAction;
use Filament\Facades\Filament;
use Livewire\Livewire;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\get;

/**
 * Initial setup for the tests.
 */
beforeEach(function () {
    Filament::setServingStatus();
});

test('render view page', function () {
    $user = User::factory()
        ->withAdmin()
        ->withPermissions(
            SpecialPermission::VIEW_FILAMENT->value,
            CrudPermission::VIEW->format(ExternalProfileModel::class)
        )
        ->createOne();

    actingAs($user);

    $record = ExternalProfileModel::factory()->createOne();

    get(ExternalProfileResource::getUrl('view', ['record' => $record]))
        ->assertSuccessful();
});

test('render index page', function () {
    $user = User::factory()
        ->withAdmin()
        ->withPermissions(
            SpecialPermission::VIEW_FILAMENT->value,
            CrudPermission::VIEW->format(ExternalProfileModel::class)
        )
        ->createOne();

    actingAs($user);

    $records = ExternalProfileModel::factory()->count(10)->create();

    get(ExternalProfileResource::getUrl('index'))
        ->assertSuccessful();

    Livewire::test(getIndexPage(ExternalProfileResource::class))
        ->assertCanSeeTableRecords($records);
});

test('mount create action', function () {
    $user = User::factory()
        ->withAdmin()
        ->withPermissions(
            SpecialPermission::VIEW_FILAMENT->value,
            CrudPermission::CREATE->format(ExternalProfileModel::class)
        )
        ->createOne();

    actingAs($user);

    Livewire::test(getIndexPage(ExternalProfileResource::class))
        ->mountAction(CreateAction::class)
        ->assertActionMounted(CreateAction::class);
});

test('mount edit action', function () {
    $user = User::factory()
        ->withAdmin()
        ->withPermissions(
            SpecialPermission::VIEW_FILAMENT->value,
            CrudPermission::UPDATE->format(ExternalProfileModel::class),
        )
        ->createOne();

    actingAs($user);

    $record = ExternalProfileModel::factory()->createOne();

    Livewire::test(getIndexPage(ExternalProfileResource::class))
        ->mountAction(TestAction::make(EditAction::getDefaultName())->table($record))
        ->callMountedAction()
        ->assertHasNoErrors();
});
