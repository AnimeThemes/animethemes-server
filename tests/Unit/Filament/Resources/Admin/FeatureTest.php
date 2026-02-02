<?php

declare(strict_types=1);

use App\Enums\Auth\CrudPermission;
use App\Enums\Auth\SpecialPermission;
use App\Filament\Actions\Base\DeleteAction;
use App\Filament\Actions\Base\EditAction;
use App\Filament\Resources\Admin\FeatureResource;
use App\Models\Admin\Feature as FeatureModel;
use App\Models\Auth\User;
use Filament\Actions\Testing\TestAction;
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

    get(FeatureResource::getUrl('index'))
        ->assertSuccessful();

    Livewire::test(getIndexPage(FeatureResource::class))
        ->assertCanSeeTableRecords($records);
});

test('user cannot edit record', function () {
    $record = FeatureModel::factory()->createOne();

    Livewire::test(getIndexPage(FeatureResource::class))
        ->assertActionDoesNotExist(TestAction::make(EditAction::getDefaultName())->table($record));
});

test('user cannot delete record', function () {
    $record = FeatureModel::factory()->createOne();

    Livewire::test(getIndexPage(FeatureResource::class))
        ->assertActionDoesNotExist(TestAction::make(DeleteAction::getDefaultName())->table($record));
});
