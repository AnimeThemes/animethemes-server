<?php

declare(strict_types=1);

namespace Tests\Unit\Policies\Wiki;

use App\Models\Auth\User;
use App\Models\Wiki\Anime;
use App\Models\Wiki\Image;
use App\Models\Wiki\Series;
use App\Models\Wiki\Studio;
use App\Policies\Wiki\AnimePolicy;
use Illuminate\Foundation\Testing\WithoutEvents;
use Tests\TestCase;

/**
 * Class AnimePolicyTest.
 */
class AnimePolicyTest extends TestCase
{
    use WithoutEvents;

    /**
     * Any user regardless of role can view any anime.
     *
     * @return void
     */
    public function testViewAny(): void
    {
        $policy = new AnimePolicy();

        static::assertTrue($policy->viewAny());
    }

    /**
     * Any user regardless of role can view an anime.
     *
     * @return void
     */
    public function testView(): void
    {
        $policy = new AnimePolicy();

        static::assertTrue($policy->view());
    }

    /**
     * A contributor or admin may create an anime.
     *
     * @return void
     */
    public function testCreate(): void
    {
        $viewer = User::factory()
            ->withCurrentTeam('viewer')
            ->createOne();

        $editor = User::factory()
            ->withCurrentTeam('editor')
            ->createOne();

        $admin = User::factory()
            ->withCurrentTeam('admin')
            ->createOne();

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
    public function testUpdate(): void
    {
        $viewer = User::factory()
            ->withCurrentTeam('viewer')
            ->createOne();

        $editor = User::factory()
            ->withCurrentTeam('editor')
            ->createOne();

        $admin = User::factory()
            ->withCurrentTeam('admin')
            ->createOne();

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
    public function testDelete(): void
    {
        $viewer = User::factory()
            ->withCurrentTeam('viewer')
            ->createOne();

        $editor = User::factory()
            ->withCurrentTeam('editor')
            ->createOne();

        $admin = User::factory()
            ->withCurrentTeam('admin')
            ->createOne();

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
    public function testRestore(): void
    {
        $viewer = User::factory()
            ->withCurrentTeam('viewer')
            ->createOne();

        $editor = User::factory()
            ->withCurrentTeam('editor')
            ->createOne();

        $admin = User::factory()
            ->withCurrentTeam('admin')
            ->createOne();

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
    public function testForceDelete(): void
    {
        $viewer = User::factory()
            ->withCurrentTeam('viewer')
            ->createOne();

        $editor = User::factory()
            ->withCurrentTeam('editor')
            ->createOne();

        $admin = User::factory()
            ->withCurrentTeam('admin')
            ->createOne();

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
    public function testAttachAnySeries(): void
    {
        $viewer = User::factory()
            ->withCurrentTeam('viewer')
            ->createOne();

        $editor = User::factory()
            ->withCurrentTeam('editor')
            ->createOne();

        $admin = User::factory()
            ->withCurrentTeam('admin')
            ->createOne();

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
    public function testAttachNewSeries(): void
    {
        $viewer = User::factory()
            ->withCurrentTeam('viewer')
            ->createOne();

        $editor = User::factory()
            ->withCurrentTeam('editor')
            ->createOne();

        $admin = User::factory()
            ->withCurrentTeam('admin')
            ->createOne();

        $anime = Anime::factory()->createOne();
        $series = Series::factory()->createOne();
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
    public function testAttachExistingSeries(): void
    {
        $viewer = User::factory()
            ->withCurrentTeam('viewer')
            ->createOne();

        $editor = User::factory()
            ->withCurrentTeam('editor')
            ->createOne();

        $admin = User::factory()
            ->withCurrentTeam('admin')
            ->createOne();

        $anime = Anime::factory()->createOne();
        $series = Series::factory()->createOne();
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
    public function testDetachSeries(): void
    {
        $viewer = User::factory()
            ->withCurrentTeam('viewer')
            ->createOne();

        $editor = User::factory()
            ->withCurrentTeam('editor')
            ->createOne();

        $admin = User::factory()
            ->withCurrentTeam('admin')
            ->createOne();

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
    public function testAttachAnyResource(): void
    {
        $viewer = User::factory()
            ->withCurrentTeam('viewer')
            ->createOne();

        $editor = User::factory()
            ->withCurrentTeam('editor')
            ->createOne();

        $admin = User::factory()
            ->withCurrentTeam('admin')
            ->createOne();

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
    public function testAttachResource(): void
    {
        $viewer = User::factory()
            ->withCurrentTeam('viewer')
            ->createOne();

        $editor = User::factory()
            ->withCurrentTeam('editor')
            ->createOne();

        $admin = User::factory()
            ->withCurrentTeam('admin')
            ->createOne();

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
    public function testDetachResource(): void
    {
        $viewer = User::factory()
            ->withCurrentTeam('viewer')
            ->createOne();

        $editor = User::factory()
            ->withCurrentTeam('editor')
            ->createOne();

        $admin = User::factory()
            ->withCurrentTeam('admin')
            ->createOne();

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
    public function testAttachAnyImage(): void
    {
        $viewer = User::factory()
            ->withCurrentTeam('viewer')
            ->createOne();

        $editor = User::factory()
            ->withCurrentTeam('editor')
            ->createOne();

        $admin = User::factory()
            ->withCurrentTeam('admin')
            ->createOne();

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
    public function testAttachNewImage(): void
    {
        $viewer = User::factory()
            ->withCurrentTeam('viewer')
            ->createOne();

        $editor = User::factory()
            ->withCurrentTeam('editor')
            ->createOne();

        $admin = User::factory()
            ->withCurrentTeam('admin')
            ->createOne();

        $anime = Anime::factory()->createOne();
        $image = Image::factory()->createOne();
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
    public function testAttachExistingImage(): void
    {
        $viewer = User::factory()
            ->withCurrentTeam('viewer')
            ->createOne();

        $editor = User::factory()
            ->withCurrentTeam('editor')
            ->createOne();

        $admin = User::factory()
            ->withCurrentTeam('admin')
            ->createOne();

        $anime = Anime::factory()->createOne();
        $image = Image::factory()->createOne();
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
    public function testDetachImage(): void
    {
        $viewer = User::factory()
            ->withCurrentTeam('viewer')
            ->createOne();

        $editor = User::factory()
            ->withCurrentTeam('editor')
            ->createOne();

        $admin = User::factory()
            ->withCurrentTeam('admin')
            ->createOne();

        $policy = new AnimePolicy();

        static::assertFalse($policy->detachImage($viewer));
        static::assertTrue($policy->detachImage($editor));
        static::assertTrue($policy->detachImage($admin));
    }

    /**
     * A contributor or admin may attach any studio to an anime.
     *
     * @return void
     */
    public function testAttachAnyStudio(): void
    {
        $viewer = User::factory()
            ->withCurrentTeam('viewer')
            ->createOne();

        $editor = User::factory()
            ->withCurrentTeam('editor')
            ->createOne();

        $admin = User::factory()
            ->withCurrentTeam('admin')
            ->createOne();

        $policy = new AnimePolicy();

        static::assertFalse($policy->attachAnyStudio($viewer));
        static::assertTrue($policy->attachAnyStudio($editor));
        static::assertTrue($policy->attachAnyStudio($admin));
    }

    /**
     * A contributor or admin may attach a studio to an anime if not already attached.
     *
     * @return void
     */
    public function testAttachNewStudio(): void
    {
        $viewer = User::factory()
            ->withCurrentTeam('viewer')
            ->createOne();

        $editor = User::factory()
            ->withCurrentTeam('editor')
            ->createOne();

        $admin = User::factory()
            ->withCurrentTeam('admin')
            ->createOne();

        $anime = Anime::factory()->createOne();
        $studio = Studio::factory()->createOne();
        $policy = new AnimePolicy();

        static::assertFalse($policy->attachStudio($viewer, $anime, $studio));
        static::assertTrue($policy->attachStudio($editor, $anime, $studio));
        static::assertTrue($policy->attachStudio($admin, $anime, $studio));
    }

    /**
     * If a studio is already attached to an anime, no role may attach it.
     *
     * @return void
     */
    public function testAttachExistingStudio(): void
    {
        $viewer = User::factory()
            ->withCurrentTeam('viewer')
            ->createOne();

        $editor = User::factory()
            ->withCurrentTeam('editor')
            ->createOne();

        $admin = User::factory()
            ->withCurrentTeam('admin')
            ->createOne();

        $anime = Anime::factory()->createOne();
        $studio = Studio::factory()->createOne();
        $anime->studios()->attach($studio);
        $policy = new AnimePolicy();

        static::assertFalse($policy->attachStudio($viewer, $anime, $studio));
        static::assertFalse($policy->attachStudio($editor, $anime, $studio));
        static::assertFalse($policy->attachStudio($admin, $anime, $studio));
    }

    /**
     * A contributor or admin may detach a studio from an anime.
     *
     * @return void
     */
    public function testDetachStudio(): void
    {
        $viewer = User::factory()
            ->withCurrentTeam('viewer')
            ->createOne();

        $editor = User::factory()
            ->withCurrentTeam('editor')
            ->createOne();

        $admin = User::factory()
            ->withCurrentTeam('admin')
            ->createOne();

        $policy = new AnimePolicy();

        static::assertFalse($policy->detachStudio($viewer));
        static::assertTrue($policy->detachStudio($editor));
        static::assertTrue($policy->detachStudio($admin));
    }
}
