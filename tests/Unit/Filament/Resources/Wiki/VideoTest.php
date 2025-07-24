<?php

declare(strict_types=1);

namespace Tests\Unit\Filament\Resources\Wiki;

use App\Enums\Auth\CrudPermission;
use App\Enums\Auth\SpecialPermission;
use App\Filament\Actions\Base\DeleteAction;
use App\Filament\Actions\Base\EditAction;
use App\Filament\Actions\Base\ForceDeleteAction;
use App\Filament\Actions\Base\RestoreAction;
use App\Filament\Resources\Wiki\Video;
use App\Models\Auth\User;
use App\Models\Wiki\Video as VideoModel;
use Livewire\Livewire;
use Tests\Unit\Filament\BaseResourceTestCase;

class VideoTest extends BaseResourceTestCase
{
    /**
     * Get the index page class of the resource.
     */
    protected static function getIndexPage(): string
    {
        $pages = Video::getPages();

        return $pages['index']->getPage();
    }

    /**
     * Get the edit page class of the resource.
     */
    protected static function getEditPage(): string
    {
        $pages = Video::getPages();

        return $pages['edit']->getPage();
    }

    /**
     * Get the view page class of the resource.
     */
    protected static function getViewPage(): string
    {
        $pages = Video::getPages();

        return $pages['view']->getPage();
    }

    /**
     * The index page of the resource shall be rendered.
     */
    public function testRenderIndexPage(): void
    {
        $user = User::factory()
            ->withPermissions(
                SpecialPermission::VIEW_FILAMENT->value,
                CrudPermission::VIEW->format(VideoModel::class)
            )
            ->createOne();

        $this->actingAs($user);

        $records = VideoModel::factory()->count(10)->create();

        $this->get(Video::getUrl('index'))
            ->assertSuccessful();

        Livewire::test(static::getIndexPage())
            ->assertCanSeeTableRecords($records);
    }

    /**
     * The view page of the resource shall be rendered.
     */
    public function testRenderViewPage(): void
    {
        $user = User::factory()
            ->withPermissions(
                SpecialPermission::VIEW_FILAMENT->value,
                CrudPermission::VIEW->format(VideoModel::class)
            )
            ->createOne();

        $this->actingAs($user);

        $record = VideoModel::factory()->createOne();

        $this->get(Video::getUrl('view', ['record' => $record]))
            ->assertSuccessful();
    }

    /**
     * The create action of the resource shall be mounted.
     */
    public function testMountEditAction(): void
    {
        $user = User::factory()
            ->withPermissions(
                SpecialPermission::VIEW_FILAMENT->value,
                CrudPermission::UPDATE->format(VideoModel::class)
            )
            ->createOne();

        $this->actingAs($user);

        $record = VideoModel::factory()->createOne();

        Livewire::test(static::getIndexPage())
            ->mountAction(EditAction::class, ['record' => $record])
            ->assertActionMounted(EditAction::class);
    }

    /**
     * The user with no permissions cannot edit a record.
     */
    public function testUserCannotEditRecord(): void
    {
        $record = VideoModel::factory()->createOne();

        Livewire::test(static::getIndexPage())
            ->assertActionHidden(EditAction::class, ['record' => $record->getKey()]);
    }

    /**
     * The user with no permissions cannot delete a record.
     */
    public function testUserCannotDeleteRecord(): void
    {
        $record = VideoModel::factory()->createOne();

        Livewire::test(static::getViewPage(), ['record' => $record->getKey()])
            ->assertActionHidden(DeleteAction::class);

        Livewire::test(static::getIndexPage())
            ->assertActionHidden(DeleteAction::class, ['record' => $record->getKey()]);
    }

    /**
     * The user with no permissions cannot restore a record.
     */
    public function testUserCannotRestoreRecord(): void
    {
        $record = VideoModel::factory()->createOne();

        $record->delete();

        Livewire::test(static::getViewPage(), ['record' => $record->getKey()])
            ->assertActionHidden(RestoreAction::class);

        Livewire::test(static::getIndexPage())
            ->assertActionHidden(RestoreAction::class, ['record' => $record->getKey()]);
    }

    /**
     * The user with no permissions cannot force delete a record.
     */
    public function testUserCannotForceDeleteRecord(): void
    {
        $record = VideoModel::factory()->createOne();

        Livewire::test(static::getViewPage(), ['record' => $record->getKey()])
            ->assertActionHidden(ForceDeleteAction::class);

        Livewire::test(static::getIndexPage())
            ->assertActionHidden(ForceDeleteAction::class, ['record' => $record->getKey()]);
    }
}
