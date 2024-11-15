<?php

declare(strict_types=1);

namespace Tests\Unit\Filament\Resources\Admin;

use App\Enums\Auth\CrudPermission;
use App\Enums\Auth\SpecialPermission;
use App\Filament\HeaderActions\Base\DeleteHeaderAction;
use App\Filament\HeaderActions\Base\ForceDeleteHeaderAction;
use App\Filament\HeaderActions\Base\RestoreHeaderAction;
use App\Filament\Resources\Admin\FeaturedTheme;
use App\Models\Admin\FeaturedTheme as FeaturedThemeModel;
use App\Models\Auth\User;
use Livewire\Livewire;
use Tests\Unit\Filament\BaseResourceTest;

/**
 * Class FeaturedThemeTest.
 */
class FeaturedThemeTest extends BaseResourceTest
{
    /**
     * Get the index page class of the resource.
     *
     * @return string
     */
    protected static function getIndexPage(): string
    {
        $pages = FeaturedTheme::getPages();

        return $pages['index']->getPage();
    }

    /**
     * Get the view page class of the resource.
     *
     * @return string
     */
    protected static function getViewPage(): string
    {
        $pages = FeaturedTheme::getPages();

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
                CrudPermission::VIEW->format(FeaturedThemeModel::class)
            )
            ->createOne();

        $this->actingAs($user);

        $records = FeaturedThemeModel::factory()->count(10)->create();

        $this->get(FeaturedTheme::getUrl('index'))
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
                CrudPermission::CREATE->format(FeaturedThemeModel::class)
            )
            ->createOne();

        $this->actingAs($user);

        $this->get(FeaturedTheme::getUrl('create'))
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
                CrudPermission::UPDATE->format(FeaturedThemeModel::class)
            )
            ->createOne();

        $this->actingAs($user);

        $record = FeaturedThemeModel::factory()->createOne();

        $this->get(FeaturedTheme::getUrl('edit', ['record' => $record]))
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
                CrudPermission::VIEW->format(FeaturedThemeModel::class)
            )
            ->createOne();

        $this->actingAs($user);

        $record = FeaturedThemeModel::factory()->createOne();

        $this->get(FeaturedTheme::getUrl('view', ['record' => $record]))
            ->assertSuccessful();
    }

    /**
     * The user with no permissions cannot create a record.
     *
     * @return void
     */
    public function testUserCannotCreateRecord(): void
    {
        $this->get(FeaturedTheme::getUrl('create'))
            ->assertForbidden();
    }

    /**
     * The user with no permissions cannot edit a record.
     *
     * @return void
     */
    public function testUserCannotEditRecord(): void
    {
        $record = FeaturedThemeModel::factory()->createOne();

        $this->get(FeaturedTheme::getUrl('edit', ['record' => $record]))
            ->assertForbidden();
    }

    /**
     * The user with no permissions cannot delete a record.
     *
     * @return void
     */
    public function testUserCannotDeleteRecord(): void
    {
        $record = FeaturedThemeModel::factory()->createOne();

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
        $record = FeaturedThemeModel::factory()->createOne();

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
        $record = FeaturedThemeModel::factory()->createOne();

        Livewire::test(static::getViewPage(), ['record' => $record->getKey()])
            ->assertActionHidden(ForceDeleteHeaderAction::class);
    }
}
