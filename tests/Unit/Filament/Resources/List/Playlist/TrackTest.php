<?php

declare(strict_types=1);

namespace Tests\Unit\Filament\Resources\List\Playlist;

use App\Enums\Auth\CrudPermission;
use App\Enums\Auth\SpecialPermission;
use App\Filament\Actions\Base\EditAction;
use App\Filament\HeaderActions\Base\CreateHeaderAction;
use App\Filament\Resources\List\Playlist\Track;
use App\Models\Auth\User;
use App\Models\List\Playlist;
use App\Models\List\Playlist\PlaylistTrack as PlaylistTrackModel;
use Filament\Facades\Filament;
use Livewire\Livewire;
use Tests\Unit\Filament\BaseResourceTestCase;

/**
 * Class TrackTest.
 */
class TrackTest extends BaseResourceTestCase
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
        $pages = Track::getPages();

        return $pages['index']->getPage();
    }

    /**
     * Get the view page class of the resource.
     *
     * @return string
     */
    protected static function getViewPage(): string
    {
        $pages = Track::getPages();

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
                CrudPermission::VIEW->format(PlaylistTrackModel::class)
            )
            ->createOne();

        $this->actingAs($user);

        $playlist = Playlist::factory()->tracks(3)->create();

        $records = $playlist->tracks;

        $this->get(Track::getUrl('index'))
            ->assertSuccessful();

        Livewire::test(static::getIndexPage())
            ->assertCanSeeTableRecords($records);
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
                CrudPermission::VIEW->format(PlaylistTrackModel::class)
            )
            ->createOne();

        $this->actingAs($user);

        $playlist = Playlist::factory()->tracks(3)->create();

        $record = $playlist->tracks->first();

        $this->get(Track::getUrl('view', ['record' => $record]))
            ->assertSuccessful();
    }

    /**
     * The create action of the resource shall be mounted.
     *
     * @return void
     */
    public function testMountCreateAction(): void
    {
        $user = User::factory()
            ->withAdmin()
            ->withPermissions(
                SpecialPermission::VIEW_FILAMENT->value,
                CrudPermission::CREATE->format(PlaylistTrackModel::class)
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
    public function testMountEditAction(): void
    {
        $user = User::factory()
            ->withAdmin()
            ->withPermissions(
                SpecialPermission::VIEW_FILAMENT->value,
                CrudPermission::UPDATE->format(PlaylistTrackModel::class),
            )
            ->createOne();

        $this->actingAs($user);

        $playlist = Playlist::factory()->tracks(3)->create();

        $record = $playlist->tracks->first();

        Livewire::test(static::getIndexPage())
            ->mountTableAction(EditAction::class, $record)
            ->assertTableActionMounted(EditAction::class);
    }
}
