<?php

declare(strict_types=1);

namespace Tests\Unit\Filament\Resources\Wiki\Anime;

use App\Enums\Auth\CrudPermission;
use App\Enums\Auth\SpecialPermission;
use App\Filament\HeaderActions\Base\DeleteHeaderAction;
use App\Filament\HeaderActions\Base\ForceDeleteHeaderAction;
use App\Filament\HeaderActions\Base\RestoreHeaderAction;
use App\Filament\Resources\Wiki\Anime\Theme;
use App\Models\Auth\User;
use App\Models\Wiki\Anime;
use App\Models\Wiki\Anime\AnimeTheme as AnimeThemeModel;
use Livewire\Livewire;
use Tests\Unit\Filament\BaseResourceTest;

/**
 * Class ThemeTest.
 */
class ThemeTest extends BaseResourceTest
{
    /**
     * Get the index page class of the resource.
     *
     * @return string
     */
    protected static function getIndexPage(): string
    {
        $pages = Theme::getPages();

        return $pages['index']->getPage();
    }

    /**
     * Get the view page class of the resource.
     *
     * @return string
     */
    protected static function getViewPage(): string
    {
        $pages = Theme::getPages();

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
                CrudPermission::VIEW->format(AnimeThemeModel::class)
            )
            ->createOne();

        $this->actingAs($user);

        $records = AnimeThemeModel::factory()
            ->for(Anime::factory())
            ->count(10)->create();

        $this->get(Theme::getUrl('index'))
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
                CrudPermission::CREATE->format(AnimeThemeModel::class)
            )
            ->createOne();

        $this->actingAs($user);

        $this->get(Theme::getUrl('create'))
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
                CrudPermission::UPDATE->format(AnimeThemeModel::class)
            )
            ->createOne();

        $this->actingAs($user);

        $record = AnimeThemeModel::factory()
            ->for(Anime::factory())
            ->createOne();

        $this->get(Theme::getUrl('edit', ['record' => $record]))
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
                CrudPermission::VIEW->format(AnimeThemeModel::class)
            )
            ->createOne();

        $this->actingAs($user);

        $record = AnimeThemeModel::factory()
            ->for(Anime::factory())
            ->createOne();

        $this->get(Theme::getUrl('view', ['record' => $record]))
            ->assertSuccessful();
    }

    /**
     * The user with no permissions cannot create a record.
     *
     * @return void
     */
    public function testUserCannotCreateRecord(): void
    {
        $this->get(Theme::getUrl('create'))
            ->assertForbidden();
    }

    /**
     * The user with no permissions cannot edit a record.
     *
     * @return void
     */
    public function testUserCannotEditRecord(): void
    {
        $record = AnimeThemeModel::factory()
            ->for(Anime::factory())
            ->createOne();

        $this->get(Theme::getUrl('edit', ['record' => $record]))
            ->assertForbidden();
    }

    /**
     * The user with no permissions cannot delete a record.
     *
     * @return void
     */
    public function testUserCannotDeleteRecord(): void
    {
        $record = AnimeThemeModel::factory()
            ->for(Anime::factory())
            ->createOne();

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
        $record = AnimeThemeModel::factory()
            ->for(Anime::factory())
            ->createOne();

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
        $record = AnimeThemeModel::factory()
            ->for(Anime::factory())
            ->createOne();

        Livewire::test(static::getViewPage(), ['record' => $record->getKey()])
            ->assertActionHidden(ForceDeleteHeaderAction::class);
    }
}
