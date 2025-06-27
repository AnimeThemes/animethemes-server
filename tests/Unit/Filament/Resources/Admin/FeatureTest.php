<?php

declare(strict_types=1);

namespace Tests\Unit\Filament\Resources\Admin;

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
use Tests\Unit\Filament\BaseResourceTestCase;

/**
 * Class FeatureTest.
 */
class FeatureTest extends BaseResourceTestCase
{
    /**
     * Get the index page class of the resource.
     *
     * @return string
     */
    protected static function getIndexPage(): string
    {
        $pages = Feature::getPages();

        return $pages['index']->getPage();
    }

    /**
     * The index page of the resource shall be rendered.
     *
     * @return void
     */
    public function test_render_index_page(): void
    {
        $user = User::factory()
            ->withPermissions(
                SpecialPermission::VIEW_FILAMENT->value,
                CrudPermission::VIEW->format(FeatureModel::class)
            )
            ->createOne();

        $this->actingAs($user);

        $records = FeatureModel::factory()->count(10)->create();

        $this->get(Feature::getUrl('index'))
            ->assertSuccessful();

        Livewire::test(static::getIndexPage())
            ->assertCanSeeTableRecords($records);
    }

    /**
     * The user with no permissions cannot edit a record.
     *
     * @return void
     */
    public function test_user_cannot_edit_record(): void
    {
        $record = FeatureModel::factory()->createOne();

        Livewire::test(static::getIndexPage())
            ->assertTableActionHidden(EditAction::class, $record);
    }

    /**
     * The user with no permissions cannot delete a record.
     *
     * @return void
     */
    public function test_user_cannot_delete_record(): void
    {
        $record = FeatureModel::factory()->createOne();

        Livewire::test(static::getIndexPage())
            ->assertTableActionHidden(DeleteAction::class, $record);
    }

    /**
     * The user with no permissions cannot restore a record.
     *
     * @return void
     */
    public function test_user_cannot_restore_record(): void
    {
        $record = FeatureModel::factory()->createOne();

        Livewire::test(static::getIndexPage())
            ->assertTableActionHidden(RestoreAction::class, $record);
    }

    /**
     * The user with no permissions cannot force delete a record.
     *
     * @return void
     */
    public function test_user_cannot_force_delete_record(): void
    {
        $record = FeatureModel::factory()->createOne();

        Livewire::test(static::getIndexPage())
            ->assertTableActionHidden(ForceDeleteAction::class, $record);
    }
}
