<?php

declare(strict_types=1);

use App\Enums\Auth\CrudPermission;
use App\Enums\Auth\SpecialPermission;
use App\Filament\Actions\Base\EditAction;
use App\Filament\Resources\List\External\ExternalEntry;
use App\Models\Auth\User;
use App\Models\List\External\ExternalEntry as ExternalEntryModel;
use App\Models\List\ExternalProfile;
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

test('render index page', function () {
    $user = User::factory()
        ->withAdmin()
        ->withPermissions(
            SpecialPermission::VIEW_FILAMENT->value,
            CrudPermission::VIEW->format(ExternalEntryModel::class)
        )
        ->createOne();

    actingAs($user);

    $profile = ExternalProfile::factory()->entries(3)->createOne();

    $records = $profile->externalentries;

    get(ExternalEntry::getUrl('index'))
        ->assertSuccessful();

    Livewire::test(getIndexPage(ExternalEntry::class))
        ->assertCanSeeTableRecords($records);
});

test('render view page', function () {
    $user = User::factory()
        ->withAdmin()
        ->withPermissions(
            SpecialPermission::VIEW_FILAMENT->value,
            CrudPermission::VIEW->format(ExternalEntryModel::class)
        )
        ->createOne();

    actingAs($user);

    $profile = ExternalProfile::factory()->entries(3)->createOne();

    $record = $profile->externalentries->first();

    get(ExternalEntry::getUrl('view', ['record' => $record]))
        ->assertSuccessful();
});

test('mount edit action', function () {
    $user = User::factory()
        ->withAdmin()
        ->withPermissions(
            SpecialPermission::VIEW_FILAMENT->value,
            CrudPermission::UPDATE->format(ExternalEntryModel::class),
        )
        ->createOne();

    actingAs($user);

    $profile = ExternalProfile::factory()->entries(3)->createOne();

    $record = $profile->externalentries->first();

    Livewire::test(getIndexPage(ExternalEntry::class))
        ->mountAction(TestAction::make(EditAction::getDefaultName())->table($record))
        ->callMountedAction()
        ->assertHasNoErrors();
});
