<?php

declare(strict_types=1);

namespace Tests\Unit\Filament\Resources\Admin;

use App\Enums\Auth\CrudPermission;
use App\Enums\Auth\SpecialPermission;
use App\Filament\Actions\Base\DeleteAction;
use App\Filament\Actions\Base\EditAction;
use App\Filament\Actions\Base\ForceDeleteAction;
use App\Filament\Actions\Base\RestoreAction;
use App\Filament\HeaderActions\Base\CreateHeaderAction;
use App\Filament\Resources\Admin\Announcement;
use App\Models\Admin\Announcement as AnnouncementModel;
use App\Models\Auth\User;
use Livewire\Livewire;
use Tests\Unit\Filament\BaseResourceTestCase;

/**
 * Class AnnouncementTest.
 */
class AnnouncementTest extends BaseResourceTestCase
{
    /**
     * Get the index page class of the resource.
     *
     * @return string
     */
    protected static function getIndexPage(): string
    {
        $pages = Announcement::getPages();

        return $pages['index']->getPage();
    }

    /**
     * The index page of the resource shall be rendered.
     *
     * @return void
     */
    public function testRenderIndexPage(): void
    {
        $user = User::factory()
            ->withPermissions(
                SpecialPermission::VIEW_FILAMENT->value,
                CrudPermission::VIEW->format(AnnouncementModel::class)
            )
            ->createOne();

        $this->actingAs($user);

        $records = AnnouncementModel::factory()->count(10)->create();

        $this->get(Announcement::getUrl('index'))
            ->assertSuccessful();

        Livewire::test(static::getIndexPage())
            ->assertCanSeeTableRecords($records);
    }

    /**
     * The user with no permissions cannot create a record.
     *
     * @return void
     */
    public function testUserCannotCreateRecord(): void
    {
        Livewire::test(static::getIndexPage())
            ->assertActionHidden(CreateHeaderAction::class);
    }

    /**
     * The user with no permissions cannot edit a record.
     *
     * @return void
     */
    public function testUserCannotEditRecord(): void
    {
        $record = AnnouncementModel::factory()->createOne();

        Livewire::test(static::getIndexPage())
            ->assertTableActionHidden(EditAction::class, $record);
    }

    /**
     * The user with no permissions cannot delete a record.
     *
     * @return void
     */
    public function testUserCannotDeleteRecord(): void
    {
        $record = AnnouncementModel::factory()->createOne();

        Livewire::test(static::getIndexPage())
            ->assertTableActionHidden(DeleteAction::class, $record);
    }

    /**
     * The user with no permissions cannot restore a record.
     *
     * @return void
     */
    public function testUserCannotRestoreRecord(): void
    {
        $record = AnnouncementModel::factory()->createOne();

        Livewire::test(static::getIndexPage())
            ->assertTableActionHidden(RestoreAction::class, $record);
    }

    /**
     * The user with no permissions cannot force delete a record.
     *
     * @return void
     */
    public function testUserCannotForceDeleteRecord(): void
    {
        $record = AnnouncementModel::factory()->createOne();

        Livewire::test(static::getIndexPage())
            ->assertTableActionHidden(ForceDeleteAction::class, $record);
    }
}
