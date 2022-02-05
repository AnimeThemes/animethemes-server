<?php

declare(strict_types=1);

namespace Tests\Unit\Policies\Wiki;

use App\Models\Auth\User;
use App\Models\Wiki\Artist;
use App\Models\Wiki\Image;
use App\Policies\Wiki\ArtistPolicy;
use Illuminate\Foundation\Testing\WithoutEvents;
use Tests\TestCase;

/**
 * Class ArtistPolicyTest.
 */
class ArtistPolicyTest extends TestCase
{
    use WithoutEvents;

    /**
     * Any user regardless of role can view any artist.
     *
     * @return void
     */
    public function testViewAny(): void
    {
        $policy = new ArtistPolicy();

        static::assertTrue($policy->viewAny());
    }

    /**
     * Any user regardless of role can view an artist.
     *
     * @return void
     */
    public function testView(): void
    {
        $policy = new ArtistPolicy();

        static::assertTrue($policy->view());
    }

    /**
     * A contributor or admin may create an artist.
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

        $policy = new ArtistPolicy();

        static::assertFalse($policy->create($viewer));
        static::assertTrue($policy->create($editor));
        static::assertTrue($policy->create($admin));
    }

    /**
     * A contributor or admin may update an artist.
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

        $policy = new ArtistPolicy();

        static::assertFalse($policy->update($viewer));
        static::assertTrue($policy->update($editor));
        static::assertTrue($policy->update($admin));
    }

    /**
     * A contributor or admin may delete an artist.
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

        $policy = new ArtistPolicy();

        static::assertFalse($policy->delete($viewer));
        static::assertTrue($policy->delete($editor));
        static::assertTrue($policy->delete($admin));
    }

    /**
     * A contributor or admin may restore an artist.
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

        $policy = new ArtistPolicy();

        static::assertFalse($policy->restore($viewer));
        static::assertTrue($policy->restore($editor));
        static::assertTrue($policy->restore($admin));
    }

    /**
     * A contributor or admin may force delete an artist.
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

        $policy = new ArtistPolicy();

        static::assertFalse($policy->forceDelete($viewer));
        static::assertFalse($policy->forceDelete($editor));
        static::assertTrue($policy->forceDelete($admin));
    }

    /**
     * A contributor or admin may attach any resource to an artist.
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

        $policy = new ArtistPolicy();

        static::assertFalse($policy->attachAnyExternalResource($viewer));
        static::assertTrue($policy->attachAnyExternalResource($editor));
        static::assertTrue($policy->attachAnyExternalResource($admin));
    }

    /**
     * A contributor or admin may attach a resource to an artist.
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

        $policy = new ArtistPolicy();

        static::assertFalse($policy->attachExternalResource($viewer));
        static::assertTrue($policy->attachExternalResource($editor));
        static::assertTrue($policy->attachExternalResource($admin));
    }

    /**
     * A contributor or admin may detach a resource from an artist.
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

        $policy = new ArtistPolicy();

        static::assertFalse($policy->detachExternalResource($viewer));
        static::assertTrue($policy->detachExternalResource($editor));
        static::assertTrue($policy->detachExternalResource($admin));
    }

    /**
     * A contributor or admin may attach any song to an artist.
     *
     * @return void
     */
    public function testAttachAnySong(): void
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

        $policy = new ArtistPolicy();

        static::assertFalse($policy->attachAnySong($viewer));
        static::assertTrue($policy->attachAnySong($editor));
        static::assertTrue($policy->attachAnySong($admin));
    }

    /**
     * A contributor or admin may attach a song to an artist.
     *
     * @return void
     */
    public function testAttachSong(): void
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

        $policy = new ArtistPolicy();

        static::assertFalse($policy->attachSong($viewer));
        static::assertTrue($policy->attachSong($editor));
        static::assertTrue($policy->attachSong($admin));
    }

    /**
     * A contributor or admin may detach a song from an artist.
     *
     * @return void
     */
    public function testDetachSong(): void
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

        $policy = new ArtistPolicy();

        static::assertFalse($policy->detachSong($viewer));
        static::assertTrue($policy->detachSong($editor));
        static::assertTrue($policy->detachSong($admin));
    }

    /**
     * A contributor or admin may attach any group/member to an artist.
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

        $policy = new ArtistPolicy();

        static::assertFalse($policy->attachAnyArtist($viewer));
        static::assertTrue($policy->attachAnyArtist($editor));
        static::assertTrue($policy->attachAnyArtist($admin));
    }

    /**
     * A contributor or admin may attach a group/member to an artist.
     *
     * @return void
     */
    public function testAttachArtist(): void
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

        $policy = new ArtistPolicy();

        static::assertFalse($policy->attachArtist($viewer));
        static::assertTrue($policy->attachArtist($editor));
        static::assertTrue($policy->attachArtist($admin));
    }

    /**
     * A contributor or admin may detach a group/member from an artist.
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

        $policy = new ArtistPolicy();

        static::assertFalse($policy->detachArtist($viewer));
        static::assertTrue($policy->detachArtist($editor));
        static::assertTrue($policy->detachArtist($admin));
    }

    /**
     * A contributor or admin may attach any image to an artist.
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

        $policy = new ArtistPolicy();

        static::assertFalse($policy->attachAnyImage($viewer));
        static::assertTrue($policy->attachAnyImage($editor));
        static::assertTrue($policy->attachAnyImage($admin));
    }

    /**
     * A contributor or admin may attach an image to an artist if not already attached.
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

        $artist = Artist::factory()->createOne();
        $image = Image::factory()->createOne();
        $policy = new ArtistPolicy();

        static::assertFalse($policy->attachImage($viewer, $artist, $image));
        static::assertTrue($policy->attachImage($editor, $artist, $image));
        static::assertTrue($policy->attachImage($admin, $artist, $image));
    }

    /**
     * If an image is already attached to an artist, no role may attach it.
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

        $artist = Artist::factory()->createOne();
        $image = Image::factory()->createOne();
        $artist->images()->attach($image);
        $policy = new ArtistPolicy();

        static::assertFalse($policy->attachImage($viewer, $artist, $image));
        static::assertFalse($policy->attachImage($editor, $artist, $image));
        static::assertFalse($policy->attachImage($admin, $artist, $image));
    }

    /**
     * A contributor or admin may detach an image from an artist.
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

        $policy = new ArtistPolicy();

        static::assertFalse($policy->detachImage($viewer));
        static::assertTrue($policy->detachImage($editor));
        static::assertTrue($policy->detachImage($admin));
    }
}
