<?php

namespace Tests\Unit\Policies;

use App\Models\Anime;
use App\Models\ExternalResource;
use App\Models\Image;
use App\Models\Series;
use App\Models\User;
use App\Policies\AnimePolicy;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithoutEvents;
use Tests\TestCase;

class AnimePolicyTest extends TestCase
{
    use RefreshDatabase, WithoutEvents;

    /**
     * Any user regardless of role can view any anime.
     *
     * @return void
     */
    public function testViewAny()
    {
        $viewer = User::factory()
            ->withCurrentTeam('viewer')
            ->create();

        $editor = User::factory()
            ->withCurrentTeam('editor')
            ->create();

        $admin = User::factory()
            ->withCurrentTeam('admin')
            ->create();

        $policy = new AnimePolicy();

        $this->assertTrue($policy->viewAny($viewer));
        $this->assertTrue($policy->viewAny($editor));
        $this->assertTrue($policy->viewAny($admin));
    }

    /**
     * Any user regardless of role can view an anime.
     *
     * @return void
     */
    public function testView()
    {
        $viewer = User::factory()
            ->withCurrentTeam('viewer')
            ->create();

        $editor = User::factory()
            ->withCurrentTeam('editor')
            ->create();

        $admin = User::factory()
            ->withCurrentTeam('admin')
            ->create();

        $anime = Anime::factory()->create();
        $policy = new AnimePolicy();

        $this->assertTrue($policy->view($viewer, $anime));
        $this->assertTrue($policy->view($editor, $anime));
        $this->assertTrue($policy->view($admin, $anime));
    }

    /**
     * A contributor or admin may create an anime.
     *
     * @return void
     */
    public function testCreate()
    {
        $viewer = User::factory()
            ->withCurrentTeam('viewer')
            ->create();

        $editor = User::factory()
            ->withCurrentTeam('editor')
            ->create();

        $admin = User::factory()
            ->withCurrentTeam('admin')
            ->create();

        $policy = new AnimePolicy();

        $this->assertFalse($policy->create($viewer));
        $this->assertTrue($policy->create($editor));
        $this->assertTrue($policy->create($admin));
    }

    /**
     * A contributor or admin may update an anime.
     *
     * @return void
     */
    public function testUpdate()
    {
        $viewer = User::factory()
            ->withCurrentTeam('viewer')
            ->create();

        $editor = User::factory()
            ->withCurrentTeam('editor')
            ->create();

        $admin = User::factory()
            ->withCurrentTeam('admin')
            ->create();

        $anime = Anime::factory()->create();
        $policy = new AnimePolicy();

        $this->assertFalse($policy->update($viewer, $anime));
        $this->assertTrue($policy->update($editor, $anime));
        $this->assertTrue($policy->update($admin, $anime));
    }

    /**
     * A contributor or admin may delete an anime.
     *
     * @return void
     */
    public function testDelete()
    {
        $viewer = User::factory()
            ->withCurrentTeam('viewer')
            ->create();

        $editor = User::factory()
            ->withCurrentTeam('editor')
            ->create();

        $admin = User::factory()
            ->withCurrentTeam('admin')
            ->create();

        $anime = Anime::factory()->create();
        $policy = new AnimePolicy();

        $this->assertFalse($policy->delete($viewer, $anime));
        $this->assertTrue($policy->delete($editor, $anime));
        $this->assertTrue($policy->delete($admin, $anime));
    }

    /**
     * A contributor or admin may restore an anime.
     *
     * @return void
     */
    public function testRestore()
    {
        $viewer = User::factory()
            ->withCurrentTeam('viewer')
            ->create();

        $editor = User::factory()
            ->withCurrentTeam('editor')
            ->create();

        $admin = User::factory()
            ->withCurrentTeam('admin')
            ->create();

        $anime = Anime::factory()->create();
        $policy = new AnimePolicy();

        $this->assertFalse($policy->restore($viewer, $anime));
        $this->assertTrue($policy->restore($editor, $anime));
        $this->assertTrue($policy->restore($admin, $anime));
    }

    /**
     * A contributor or admin may force delete an anime.
     *
     * @return void
     */
    public function testForceDelete()
    {
        $viewer = User::factory()
            ->withCurrentTeam('viewer')
            ->create();

        $editor = User::factory()
            ->withCurrentTeam('editor')
            ->create();

        $admin = User::factory()
            ->withCurrentTeam('admin')
            ->create();

        $anime = Anime::factory()->create();
        $policy = new AnimePolicy();

        $this->assertFalse($policy->forceDelete($viewer, $anime));
        $this->assertFalse($policy->forceDelete($editor, $anime));
        $this->assertTrue($policy->forceDelete($admin, $anime));
    }

    /**
     * A contributor or admin may attach any series to an anime.
     *
     * @return void
     */
    public function testAttachAnySeries()
    {
        $viewer = User::factory()
            ->withCurrentTeam('viewer')
            ->create();

        $editor = User::factory()
            ->withCurrentTeam('editor')
            ->create();

        $admin = User::factory()
            ->withCurrentTeam('admin')
            ->create();

        $anime = Anime::factory()->create();
        $policy = new AnimePolicy();

        $this->assertFalse($policy->attachAnySeries($viewer, $anime));
        $this->assertTrue($policy->attachAnySeries($editor, $anime));
        $this->assertTrue($policy->attachAnySeries($admin, $anime));
    }

    /**
     * A contributor or admin may attach a series to an anime if not already attached.
     *
     * @return void
     */
    public function testAttachNewSeries()
    {
        $viewer = User::factory()
            ->withCurrentTeam('viewer')
            ->create();

        $editor = User::factory()
            ->withCurrentTeam('editor')
            ->create();

        $admin = User::factory()
            ->withCurrentTeam('admin')
            ->create();

        $anime = Anime::factory()->create();
        $series = Series::factory()->create();
        $policy = new AnimePolicy();

        $this->assertFalse($policy->attachSeries($viewer, $anime, $series));
        $this->assertTrue($policy->attachSeries($editor, $anime, $series));
        $this->assertTrue($policy->attachSeries($admin, $anime, $series));
    }

    /**
     * If a series is already attached to an anime, no role may attach it.
     *
     * @return void
     */
    public function testAttachExistingSeries()
    {
        $viewer = User::factory()
            ->withCurrentTeam('viewer')
            ->create();

        $editor = User::factory()
            ->withCurrentTeam('editor')
            ->create();

        $admin = User::factory()
            ->withCurrentTeam('admin')
            ->create();

        $anime = Anime::factory()->create();
        $series = Series::factory()->create();
        $anime->series()->attach($series);
        $policy = new AnimePolicy();

        $this->assertFalse($policy->attachSeries($viewer, $anime, $series));
        $this->assertFalse($policy->attachSeries($editor, $anime, $series));
        $this->assertFalse($policy->attachSeries($admin, $anime, $series));
    }

    /**
     * A contributor or admin may detach a series from an anime.
     *
     * @return void
     */
    public function testDetachSeries()
    {
        $viewer = User::factory()
            ->withCurrentTeam('viewer')
            ->create();

        $editor = User::factory()
            ->withCurrentTeam('editor')
            ->create();

        $admin = User::factory()
            ->withCurrentTeam('admin')
            ->create();

        $anime = Anime::factory()->create();
        $series = Series::factory()->create();
        $policy = new AnimePolicy();

        $this->assertFalse($policy->detachSeries($viewer, $anime, $series));
        $this->assertTrue($policy->detachSeries($editor, $anime, $series));
        $this->assertTrue($policy->detachSeries($admin, $anime, $series));
    }

    /**
     * A contributor or admin may attach any resource to an anime.
     *
     * @return void
     */
    public function testAttachAnyResource()
    {
        $viewer = User::factory()
            ->withCurrentTeam('viewer')
            ->create();

        $editor = User::factory()
            ->withCurrentTeam('editor')
            ->create();

        $admin = User::factory()
            ->withCurrentTeam('admin')
            ->create();

        $anime = Anime::factory()->create();
        $policy = new AnimePolicy();

        $this->assertFalse($policy->attachAnyExternalResource($viewer, $anime));
        $this->assertTrue($policy->attachAnyExternalResource($editor, $anime));
        $this->assertTrue($policy->attachAnyExternalResource($admin, $anime));
    }

    /**
     * A contributor or admin may attach a resource to an anime.
     *
     * @return void
     */
    public function testAttachResource()
    {
        $viewer = User::factory()
            ->withCurrentTeam('viewer')
            ->create();

        $editor = User::factory()
            ->withCurrentTeam('editor')
            ->create();

        $admin = User::factory()
            ->withCurrentTeam('admin')
            ->create();

        $anime = Anime::factory()->create();
        $resource = ExternalResource::factory()->create();
        $policy = new AnimePolicy();

        $this->assertFalse($policy->attachExternalResource($viewer, $anime, $resource));
        $this->assertTrue($policy->attachExternalResource($editor, $anime, $resource));
        $this->assertTrue($policy->attachExternalResource($admin, $anime, $resource));
    }

    /**
     * A contributor or admin may detach a resource from an anime.
     *
     * @return void
     */
    public function testDetachResource()
    {
        $viewer = User::factory()
            ->withCurrentTeam('viewer')
            ->create();

        $editor = User::factory()
            ->withCurrentTeam('editor')
            ->create();

        $admin = User::factory()
            ->withCurrentTeam('admin')
            ->create();

        $anime = Anime::factory()->create();
        $resource = ExternalResource::factory()->create();
        $policy = new AnimePolicy();

        $this->assertFalse($policy->detachExternalResource($viewer, $anime, $resource));
        $this->assertTrue($policy->detachExternalResource($editor, $anime, $resource));
        $this->assertTrue($policy->detachExternalResource($admin, $anime, $resource));
    }

    /**
     * A contributor or admin may attach any image to an anime.
     *
     * @return void
     */
    public function testAttachAnyImage()
    {
        $viewer = User::factory()
            ->withCurrentTeam('viewer')
            ->create();

        $editor = User::factory()
            ->withCurrentTeam('editor')
            ->create();

        $admin = User::factory()
            ->withCurrentTeam('admin')
            ->create();

        $anime = Anime::factory()->create();
        $policy = new AnimePolicy();

        $this->assertFalse($policy->attachAnyImage($viewer, $anime));
        $this->assertTrue($policy->attachAnyImage($editor, $anime));
        $this->assertTrue($policy->attachAnyImage($admin, $anime));
    }

    /**
     * A contributor or admin may attach an image to an anime if not already attached.
     *
     * @return void
     */
    public function testAttachNewImage()
    {
        $viewer = User::factory()
            ->withCurrentTeam('viewer')
            ->create();

        $editor = User::factory()
            ->withCurrentTeam('editor')
            ->create();

        $admin = User::factory()
            ->withCurrentTeam('admin')
            ->create();

        $anime = Anime::factory()->create();
        $image = Image::factory()->create();
        $policy = new AnimePolicy();

        $this->assertFalse($policy->attachImage($viewer, $anime, $image));
        $this->assertTrue($policy->attachImage($editor, $anime, $image));
        $this->assertTrue($policy->attachImage($admin, $anime, $image));
    }

    /**
     * If an image is already attached to an anime, no role may attach it.
     *
     * @return void
     */
    public function testAttachExistingImage()
    {
        $viewer = User::factory()
            ->withCurrentTeam('viewer')
            ->create();

        $editor = User::factory()
            ->withCurrentTeam('editor')
            ->create();

        $admin = User::factory()
            ->withCurrentTeam('admin')
            ->create();

        $anime = Anime::factory()->create();
        $image = Image::factory()->create();
        $anime->images()->attach($image);
        $policy = new AnimePolicy();

        $this->assertFalse($policy->attachImage($viewer, $anime, $image));
        $this->assertFalse($policy->attachImage($editor, $anime, $image));
        $this->assertFalse($policy->attachImage($admin, $anime, $image));
    }

    /**
     * A contributor or admin may detach an image from an anime.
     *
     * @return void
     */
    public function testDetachImage()
    {
        $viewer = User::factory()
            ->withCurrentTeam('viewer')
            ->create();

        $editor = User::factory()
            ->withCurrentTeam('editor')
            ->create();

        $admin = User::factory()
            ->withCurrentTeam('admin')
            ->create();

        $anime = Anime::factory()->create();
        $image = Image::factory()->create();
        $policy = new AnimePolicy();

        $this->assertFalse($policy->detachImage($viewer, $anime, $image));
        $this->assertTrue($policy->detachImage($editor, $anime, $image));
        $this->assertTrue($policy->detachImage($admin, $anime, $image));
    }
}
