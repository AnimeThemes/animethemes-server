<?php

declare(strict_types=1);

namespace Tests\Unit\Policies\Wiki;

use App\Models\Auth\User;
use App\Models\Wiki\Anime;
use App\Models\Wiki\Artist;
use App\Models\Wiki\Image;
use App\Policies\Wiki\ImagePolicy;
use Illuminate\Foundation\Testing\WithoutEvents;
use Tests\TestCase;

/**
 * Class ImagePolicyTest.
 */
class ImagePolicyTest extends TestCase
{
    use WithoutEvents;

    /**
     * Any user regardless of role can view any image.
     *
     * @return void
     */
    public function testViewAny(): void
    {
        $policy = new ImagePolicy();

        static::assertTrue($policy->viewAny());
    }

    /**
     * Any user regardless of role can view an image.
     *
     * @return void
     */
    public function testView(): void
    {
        $policy = new ImagePolicy();

        static::assertTrue($policy->view());
    }

    /**
     * A contributor or admin may create an image.
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

        $policy = new ImagePolicy();

        static::assertFalse($policy->create($viewer));
        static::assertTrue($policy->create($editor));
        static::assertTrue($policy->create($admin));
    }

    /**
     * A contributor or admin may update an image.
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

        $policy = new ImagePolicy();

        static::assertFalse($policy->update($viewer));
        static::assertTrue($policy->update($editor));
        static::assertTrue($policy->update($admin));
    }

    /**
     * A contributor or admin may delete an image.
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

        $policy = new ImagePolicy();

        static::assertFalse($policy->delete($viewer));
        static::assertTrue($policy->delete($editor));
        static::assertTrue($policy->delete($admin));
    }

    /**
     * A contributor or admin may restore an image.
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

        $policy = new ImagePolicy();

        static::assertFalse($policy->restore($viewer));
        static::assertTrue($policy->restore($editor));
        static::assertTrue($policy->restore($admin));
    }

    /**
     * A contributor or admin may force delete an image.
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

        $policy = new ImagePolicy();

        static::assertFalse($policy->forceDelete($viewer));
        static::assertFalse($policy->forceDelete($editor));
        static::assertTrue($policy->forceDelete($admin));
    }

    /**
     * A contributor or admin may attach any artist to an image.
     *
     * @return void
     */
    public function testAttachAnyArtist(): void
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

        $policy = new ImagePolicy();

        static::assertFalse($policy->attachAnyArtist($viewer));
        static::assertTrue($policy->attachAnyArtist($editor));
        static::assertTrue($policy->attachAnyArtist($admin));
    }

    /**
     * A contributor or admin may attach an artist to an image if not already attached.
     *
     * @return void
     */
    public function testAttachNewArtist(): void
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

        $image = Image::factory()->createOne();
        $artist = Artist::factory()->createOne();
        $policy = new ImagePolicy();

        static::assertFalse($policy->attachArtist($viewer, $image, $artist));
        static::assertTrue($policy->attachArtist($editor, $image, $artist));
        static::assertTrue($policy->attachArtist($admin, $image, $artist));
    }

    /**
     * If an artist is already attached to an anime, no role may attach it.
     *
     * @return void
     */
    public function testAttachExistingArtist(): void
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

        $image = Image::factory()->createOne();
        $artist = Artist::factory()->createOne();
        $image->artists()->attach($artist);
        $policy = new ImagePolicy();

        static::assertFalse($policy->attachArtist($viewer, $image, $artist));
        static::assertFalse($policy->attachArtist($editor, $image, $artist));
        static::assertFalse($policy->attachArtist($admin, $image, $artist));
    }

    /**
     * A contributor or admin may detach an artist from an image.
     *
     * @return void
     */
    public function testDetachArtist(): void
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

        $policy = new ImagePolicy();

        static::assertFalse($policy->detachArtist($viewer));
        static::assertTrue($policy->detachArtist($editor));
        static::assertTrue($policy->detachArtist($admin));
    }

    /**
     * A contributor or admin may attach any anime to an image.
     *
     * @return void
     */
    public function testAttachAnyAnime(): void
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

        $policy = new ImagePolicy();

        static::assertFalse($policy->attachAnyAnime($viewer));
        static::assertTrue($policy->attachAnyAnime($editor));
        static::assertTrue($policy->attachAnyAnime($admin));
    }

    /**
     * A contributor or admin may attach an anime to an image if not already attached.
     *
     * @return void
     */
    public function testAttachNewAnime(): void
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

        $image = Image::factory()->createOne();
        $anime = Anime::factory()->createOne();
        $policy = new ImagePolicy();

        static::assertFalse($policy->attachAnime($viewer, $image, $anime));
        static::assertTrue($policy->attachAnime($editor, $image, $anime));
        static::assertTrue($policy->attachAnime($admin, $image, $anime));
    }

    /**
     * If an anime is already attached to an anime, no role may attach it.
     *
     * @return void
     */
    public function testAttachExistingAnime(): void
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

        $image = Image::factory()->createOne();
        $anime = Anime::factory()->createOne();
        $image->anime()->attach($anime);
        $policy = new ImagePolicy();

        static::assertFalse($policy->attachAnime($viewer, $image, $anime));
        static::assertFalse($policy->attachAnime($editor, $image, $anime));
        static::assertFalse($policy->attachAnime($admin, $image, $anime));
    }

    /**
     * A contributor or admin may detach an anime from an image.
     *
     * @return void
     */
    public function testDetachAnime(): void
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

        $policy = new ImagePolicy();

        static::assertFalse($policy->detachAnime($viewer));
        static::assertTrue($policy->detachAnime($editor));
        static::assertTrue($policy->detachAnime($admin));
    }
}
