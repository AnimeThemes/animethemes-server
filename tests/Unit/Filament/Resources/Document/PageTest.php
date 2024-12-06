<?php

declare(strict_types=1);

namespace Tests\Unit\Filament\Resources\Document;

use App\Enums\Auth\CrudPermission;
use App\Enums\Auth\SpecialPermission;
use App\Filament\HeaderActions\Base\DeleteHeaderAction;
use App\Filament\HeaderActions\Base\ForceDeleteHeaderAction;
use App\Filament\HeaderActions\Base\RestoreHeaderAction;
use App\Filament\Resources\Document\Page;
use App\Models\Auth\User;
use App\Models\Document\Page as PageModel;
use Livewire\Livewire;
use Tests\Unit\Filament\BaseResourceTestCase;

/**
 * Class PageTest.
 */
class PageTest extends BaseResourceTestCase
{
    /**
     * Get the index page class of the resource.
     *
     * @return string
     */
    protected static function getIndexPage(): string
    {
        $pages = Page::getPages();

        return $pages['index']->getPage();
    }

    /**
     * Get the view page class of the resource.
     *
     * @return string
     */
    protected static function getViewPage(): string
    {
        $pages = Page::getPages();

        return $pages['view']->getPage();
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
                CrudPermission::VIEW->format(PageModel::class)
            )
            ->createOne();

        $this->actingAs($user);

        $records = PageModel::factory()->count(10)->create();

        $this->get(Page::getUrl('index'))
            ->assertSuccessful();

        Livewire::test(static::getIndexPage())
            ->assertCanSeeTableRecords($records);
    }

    /**
     * The create page of the resource shall be rendered.
     *
     * @return void
     */
    public function testRenderCreatePage(): void
    {
        $user = User::factory()
            ->withPermissions(
                SpecialPermission::VIEW_FILAMENT->value,
                CrudPermission::CREATE->format(PageModel::class)
            )
            ->createOne();

        $this->actingAs($user);

        $this->get(Page::getUrl('create'))
            ->assertSuccessful();
    }

    /**
     * The edit page of the resource shall be rendered.
     *
     * @return void
     */
    public function testRenderEditPage(): void
    {
        $user = User::factory()
            ->withPermissions(
                SpecialPermission::VIEW_FILAMENT->value,
                CrudPermission::UPDATE->format(PageModel::class)
            )
            ->createOne();

        $this->actingAs($user);

        $record = PageModel::factory()->createOne();

        $this->get(Page::getUrl('edit', ['record' => $record]))
            ->assertSuccessful();
    }

    /**
     * The view page of the resource shall be rendered.
     *
     * @return void
     */
    public function testRenderViewPage(): void
    {
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
    }

    /**
     * The user with no permissions cannot create a record.
     *
     * @return void
     */
    public function testUserCannotCreateRecord(): void
    {
        $this->get(Page::getUrl('create'))
            ->assertForbidden();
    }

    /**
     * The user with no permissions cannot edit a record.
     *
     * @return void
     */
    public function testUserCannotEditRecord(): void
    {
        $record = PageModel::factory()->createOne();

        $this->get(Page::getUrl('edit', ['record' => $record]))
            ->assertForbidden();
    }

    /**
     * The user with no permissions cannot delete a record.
     *
     * @return void
     */
    public function testUserCannotDeleteRecord(): void
    {
        $record = PageModel::factory()->createOne();

        Livewire::test(static::getViewPage(), ['record' => $record->getKey()])
            ->assertActionHidden(DeleteHeaderAction::class);
    }

    /**
     * The user with no permissions cannot restore a record.
     *
     * @return void
     */
    public function testUserCannotRestoreRecord(): void
    {
        $record = PageModel::factory()->createOne();

        $record->delete();

        Livewire::test(static::getViewPage(), ['record' => $record->getKey()])
            ->assertActionHidden(RestoreHeaderAction::class);
    }

    /**
     * The user with no permissions cannot force delete a record.
     *
     * @return void
     */
    public function testUserCannotForceDeleteRecord(): void
    {
        $record = PageModel::factory()->createOne();

        Livewire::test(static::getViewPage(), ['record' => $record->getKey()])
            ->assertActionHidden(ForceDeleteHeaderAction::class);
    }
}
