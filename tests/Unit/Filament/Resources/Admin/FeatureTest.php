<?php

declare(strict_types=1);

use App\Enums\Auth\CrudPermission;
use App\Enums\Auth\SpecialPermission;
use App\Filament\Actions\Base\DeleteAction;
use App\Filament\Actions\Base\EditAction;
use App\Filament\Actions\Base\ForceDeleteAction;
use App\Filament\Actions\Base\RestoreAction;
use App\Filament\Resources\Admin\Feature;
use App\Models\Admin\Feature as FeatureModel;
use App\Models\Auth\User;
use Livewire\Livewire;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\get;

test('render index page', function () {
    $user = User::factory()
        ->withPermissions(
            SpecialPermission::VIEW_FILAMENT->value,
            CrudPermission::VIEW->format(FeatureModel::class)
        )
        ->createOne();

    actingAs($user);

    $records = FeatureModel::factory()->count(10)->create();

    get(Feature::getUrl('index'))
        ->assertSuccessful();

    Livewire::test(getIndexPage(Feature::class))
        ->assertCanSeeTableRecords($records);
});

test('user cannot edit record', function () {
    $record = FeatureModel::factory()->createOne();

    Livewire::test(getIndexPage(Feature::class))
        ->assertActionHidden(EditAction::class, ['record' => $record->getKey()]);
});

test('user cannot delete record', function () {
    $record = FeatureModel::factory()->createOne();

    Livewire::test(getIndexPage(Feature::class))
        ->assertActionHidden(DeleteAction::class, ['record' => $record->getKey()]);
});

test('user cannot restore record', function () {
    $record = FeatureModel::factory()->createOne();

    Livewire::test(getIndexPage(Feature::class))
        ->assertActionHidden(RestoreAction::class, ['record' => $record->getKey()]);
});

test('user cannot force delete record', function () {
    $record = FeatureModel::factory()->createOne();

    Livewire::test(getIndexPage(Feature::class))
        ->assertActionHidden(ForceDeleteAction::class, ['record' => $record->getKey()]);
});
