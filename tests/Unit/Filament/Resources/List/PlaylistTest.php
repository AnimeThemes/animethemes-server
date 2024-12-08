<?php

declare(strict_types=1);

namespace Tests\Unit\Filament\Resources\List;

use App\Enums\Auth\CrudPermission;
use App\Enums\Auth\SpecialPermission;
use App\Filament\Resources\List\Playlist;
use App\Models\Auth\User;
use App\Models\List\Playlist as PlaylistModel;
use Filament\Facades\Filament;
use Livewire\Livewire;
use Tests\Unit\Filament\BaseResourceTestCase;

/**
 * Class PlaylistTest.
 */
class PlaylistTest extends BaseResourceTestCase
{
    /**
     * Initial setup for the tests.
     *
     * @return void
     */
    protected function setUp(): void
    {
        parent::setUp();

        Filament::setServingStatus();
    }

    /**
     * Get the index page class of the resource.
     *
     * @return string
     */
    protected static function getIndexPage(): string
    {
        $pages = Playlist::getPages();

        return $pages['index']->getPage();
    }

    /**
     * Get the view page class of the resource.
     *
     * @return string
     */
    protected static function getViewPage(): string
    {
        $pages = Playlist::getPages();

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
            ->withAdmin()
            ->withPermissions(
                SpecialPermission::VIEW_FILAMENT->value,
                CrudPermission::VIEW->format(PlaylistModel::class)
            )
            ->createOne();

        $this->actingAs($user);

        $records = PlaylistModel::factory()->count(10)->create();

        $this->get(Playlist::getUrl('index'))
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
            ->withAdmin()
            ->withPermissions(
                SpecialPermission::VIEW_FILAMENT->value,
                CrudPermission::CREATE->format(PlaylistModel::class)
            )
            ->createOne();

        $this->actingAs($user);

        $this->get(Playlist::getUrl('create'))
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
            ->withAdmin()
            ->withPermissions(
                SpecialPermission::VIEW_FILAMENT->value,
                CrudPermission::UPDATE->format(PlaylistModel::class),
            )
            ->createOne();

        $this->actingAs($user);

        $record = PlaylistModel::factory()->createOne();

        $this->get(Playlist::getUrl('edit', ['record' => $record]))
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
            ->withAdmin()
            ->withPermissions(
                SpecialPermission::VIEW_FILAMENT->value,
                CrudPermission::VIEW->format(PlaylistModel::class)
            )
            ->createOne();

        $this->actingAs($user);

        $record = PlaylistModel::factory()->createOne();

        $this->get(Playlist::getUrl('view', ['record' => $record]))
            ->assertSuccessful();
    }
}
