<?php

declare(strict_types=1);

namespace Policies;

use App\Models\Anime;
use App\Models\Image;
use App\Models\Series;
use App\Models\User;
use App\Policies\AnimePolicy;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithoutEvents;
use Tests\TestCase;

/**
 * Class AnimePolicyTest
 * @package Policies
 */
class AnimePolicyTest extends TestCase
{
    use RefreshDatabase;
    use WithoutEvents;

    /**
     * Any user regardless of role can view any anime.
     *
     * @return void
     */
    public function testViewAny()
    {
        $policy = new AnimePolicy();

        static::assertTrue($policy->viewAny());
        static::assertTrue($policy->viewAny());
        static::assertTrue($policy->viewAny());
    }

    /**
     * Any user regardless of role can view an anime.
     *
     * @return void
     */
    public function testView()
    {
        $policy = new AnimePolicy();

        static::assertTrue($policy->view());
        static::assertTrue($policy->view());
        static::assertTrue($policy->view());
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

        static::assertFalse($policy->create($viewer));
        static::assertTrue($policy->create($editor));
        static::assertTrue($policy->create($admin));
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

        $policy = new AnimePolicy();

        static::assertFalse($policy->update($viewer));
        static::assertTrue($policy->update($editor));
        static::assertTrue($policy->update($admin));
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

        $policy = new AnimePolicy();

        static::assertFalse($policy->delete($viewer));
        static::assertTrue($policy->delete($editor));
        static::assertTrue($policy->delete($admin));
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

        $policy = new AnimePolicy();

        static::assertFalse($policy->restore($viewer));
        static::assertTrue($policy->restore($editor));
        static::assertTrue($policy->restore($admin));
    }

    /**
     * An admin may force delete an anime.
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

        $policy = new AnimePolicy();

        static::assertFalse($policy->forceDelete($viewer));
        static::assertFalse($policy->forceDelete($editor));
        static::assertTrue($policy->forceDelete($admin));
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

        $policy = new AnimePolicy();

        static::assertFalse($policy->attachAnySeries($viewer));
        static::assertTrue($policy->attachAnySeries($editor));
        static::assertTrue($policy->attachAnySeries($admin));
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

        static::assertFalse($policy->attachSeries($viewer, $anime, $series));
        static::assertTrue($policy->attachSeries($editor, $anime, $series));
        static::assertTrue($policy->attachSeries($admin, $anime, $series));
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

        static::assertFalse($policy->attachSeries($viewer, $anime, $series));
        static::assertFalse($policy->attachSeries($editor, $anime, $series));
        static::assertFalse($policy->attachSeries($admin, $anime, $series));
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

        $policy = new AnimePolicy();

        static::assertFalse($policy->detachSeries($viewer));
        static::assertTrue($policy->detachSeries($editor));
        static::assertTrue($policy->detachSeries($admin));
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

        $policy = new AnimePolicy();

        static::assertFalse($policy->attachAnyExternalResource($viewer));
        static::assertTrue($policy->attachAnyExternalResource($editor));
        static::assertTrue($policy->attachAnyExternalResource($admin));
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

        $policy = new AnimePolicy();

        static::assertFalse($policy->attachExternalResource($viewer));
        static::assertTrue($policy->attachExternalResource($editor));
        static::assertTrue($policy->attachExternalResource($admin));
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

        $policy = new AnimePolicy();

        static::assertFalse($policy->detachExternalResource($viewer));
        static::assertTrue($policy->detachExternalResource($editor));
        static::assertTrue($policy->detachExternalResource($admin));
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

        $policy = new AnimePolicy();

        static::assertFalse($policy->attachAnyImage($viewer));
        static::assertTrue($policy->attachAnyImage($editor));
        static::assertTrue($policy->attachAnyImage($admin));
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

        static::assertFalse($policy->attachImage($viewer, $anime, $image));
        static::assertTrue($policy->attachImage($editor, $anime, $image));
        static::assertTrue($policy->attachImage($admin, $anime, $image));
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

        static::assertFalse($policy->attachImage($viewer, $anime, $image));
        static::assertFalse($policy->attachImage($editor, $anime, $image));
        static::assertFalse($policy->attachImage($admin, $anime, $image));
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

        $policy = new AnimePolicy();

        static::assertFalse($policy->detachImage($viewer));
        static::assertTrue($policy->detachImage($editor));
        static::assertTrue($policy->detachImage($admin));
    }
}
