<?php

declare(strict_types=1);

namespace Tests\Unit\Filament\Resources\Wiki;

use App\Enums\Auth\CrudPermission;
use App\Enums\Auth\SpecialPermission;
use App\Filament\Actions\Base\DeleteAction;
use App\Filament\Actions\Base\EditAction;
use App\Filament\Actions\Base\ForceDeleteAction;
use App\Filament\Actions\Base\RestoreAction;
use App\Filament\HeaderActions\Base\CreateHeaderAction;
use App\Filament\HeaderActions\Base\DeleteHeaderAction;
use App\Filament\HeaderActions\Base\ForceDeleteHeaderAction;
use App\Filament\HeaderActions\Base\RestoreHeaderAction;
use App\Filament\Resources\Wiki\Studio;
use App\Models\Auth\User;
use App\Models\Wiki\Studio as StudioModel;
use Livewire\Livewire;
use Tests\Unit\Filament\BaseResourceTestCase;

/**
 * Class StudioTest.
 */
class StudioTest extends BaseResourceTestCase
{
    /**
     * Get the index page class of the resource.
     *
     * @return string
     */
    protected static function getIndexPage(): string
    {
        $pages = Studio::getPages();

        return $pages['index']->getPage();
    }

    /**
     * Get the view page class of the resource.
     *
     * @return string
     */
    protected static function getViewPage(): string
    {
        $pages = Studio::getPages();

        return $pages['view']->getPage();
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
                CrudPermission::VIEW->format(StudioModel::class)
            )
            ->createOne();

        $this->actingAs($user);

        $records = StudioModel::factory()->count(10)->create();

        $this->get(Studio::getUrl('index'))
            ->assertSuccessful();

        Livewire::test(static::getIndexPage())
            ->assertCanSeeTableRecords($records);
    }

    /**
     * The view page of the resource shall be rendered.
     *
     * @return void
     */
    public function test_render_view_page(): void
    {
        $user = User::factory()
            ->withPermissions(
                SpecialPermission::VIEW_FILAMENT->value,
                CrudPermission::VIEW->format(StudioModel::class)
            )
            ->createOne();

        $this->actingAs($user);

        $record = StudioModel::factory()->createOne();

        $this->get(Studio::getUrl('view', ['record' => $record]))
            ->assertSuccessful();
    }

    /**
     * The create action of the resource shall be mounted.
     *
     * @return void
     */
    public function test_mount_create_action(): void
    {
        $user = User::factory()
            ->withPermissions(
                SpecialPermission::VIEW_FILAMENT->value,
                CrudPermission::CREATE->format(StudioModel::class)
            )
            ->createOne();

        $this->actingAs($user);

        Livewire::test(static::getIndexPage())
            ->mountAction(CreateHeaderAction::class)
            ->assertActionMounted(CreateHeaderAction::class);
    }

    /**
     * The create action of the resource shall be mounted.
     *
     * @return void
     */
    public function test_mount_edit_action(): void
    {
        $user = User::factory()
            ->withPermissions(
                SpecialPermission::VIEW_FILAMENT->value,
                CrudPermission::UPDATE->format(StudioModel::class)
            )
            ->createOne();

        $this->actingAs($user);

        $record = StudioModel::factory()->createOne();

        Livewire::test(static::getIndexPage())
            ->mountTableAction(EditAction::class, $record)
            ->assertTableActionMounted(EditAction::class);
    }

    /**
     * The user with no permissions cannot create a record.
     *
     * @return void
     */
    public function test_user_cannot_create_record(): void
    {
        Livewire::test(static::getIndexPage())
            ->assertActionHidden(CreateHeaderAction::class);
    }

    /**
     * The user with no permissions cannot edit a record.
     *
     * @return void
     */
    public function test_user_cannot_edit_record(): void
    {
        $record = StudioModel::factory()->createOne();

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
        $record = StudioModel::factory()->createOne();

        Livewire::test(static::getViewPage(), ['record' => $record->getKey()])
            ->assertActionHidden(DeleteHeaderAction::class);

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
        $record = StudioModel::factory()->createOne();

        $record->delete();

        Livewire::test(static::getViewPage(), ['record' => $record->getKey()])
            ->assertActionHidden(RestoreHeaderAction::class);

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
        $record = StudioModel::factory()->createOne();

        Livewire::test(static::getViewPage(), ['record' => $record->getKey()])
            ->assertActionHidden(ForceDeleteHeaderAction::class);

        Livewire::test(static::getIndexPage())
            ->assertTableActionHidden(ForceDeleteAction::class, $record);
    }
}
