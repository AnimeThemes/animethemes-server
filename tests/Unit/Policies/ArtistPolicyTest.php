<?php

namespace Tests\Unit\Policies;

use App\Models\Artist;
use App\Models\ExternalResource;
use App\Models\Image;
use App\Models\Song;
use App\Models\User;
use App\Policies\ArtistPolicy;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithoutEvents;
use Tests\TestCase;

class ArtistPolicyTest extends TestCase
{
    use RefreshDatabase, WithoutEvents;

    /**
     * Any user regardless of role can view any artist.
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

        $policy = new ArtistPolicy();

        $this->assertTrue($policy->viewAny($viewer));
        $this->assertTrue($policy->viewAny($editor));
        $this->assertTrue($policy->viewAny($admin));
    }

    /**
     * Any user regardless of role can view an artist.
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

        $artist = Artist::factory()->create();
        $policy = new ArtistPolicy();

        $this->assertTrue($policy->view($viewer, $artist));
        $this->assertTrue($policy->view($editor, $artist));
        $this->assertTrue($policy->view($admin, $artist));
    }

    /**
     * A contributor or admin may create an artist.
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

        $policy = new ArtistPolicy();

        $this->assertFalse($policy->create($viewer));
        $this->assertTrue($policy->create($editor));
        $this->assertTrue($policy->create($admin));
    }

    /**
     * A contributor or admin may update an artist.
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

        $artist = Artist::factory()->create();
        $policy = new ArtistPolicy();

        $this->assertFalse($policy->update($viewer, $artist));
        $this->assertTrue($policy->update($editor, $artist));
        $this->assertTrue($policy->update($admin, $artist));
    }

    /**
     * A contributor or admin may delete an artist.
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

        $artist = Artist::factory()->create();
        $policy = new ArtistPolicy();

        $this->assertFalse($policy->delete($viewer, $artist));
        $this->assertTrue($policy->delete($editor, $artist));
        $this->assertTrue($policy->delete($admin, $artist));
    }

    /**
     * A contributor or admin may restore an artist.
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

        $artist = Artist::factory()->create();
        $policy = new ArtistPolicy();

        $this->assertFalse($policy->restore($viewer, $artist));
        $this->assertTrue($policy->restore($editor, $artist));
        $this->assertTrue($policy->restore($admin, $artist));
    }

    /**
     * A contributor or admin may force delete an artist.
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

        $artist = Artist::factory()->create();
        $policy = new ArtistPolicy();

        $this->assertFalse($policy->forceDelete($viewer, $artist));
        $this->assertFalse($policy->forceDelete($editor, $artist));
        $this->assertTrue($policy->forceDelete($admin, $artist));
    }

    /**
     * A contributor or admin may attach any resource to an artist.
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

        $artist = Artist::factory()->create();
        $policy = new ArtistPolicy();

        $this->assertFalse($policy->attachAnyExternalResource($viewer, $artist));
        $this->assertTrue($policy->attachAnyExternalResource($editor, $artist));
        $this->assertTrue($policy->attachAnyExternalResource($admin, $artist));
    }

    /**
     * A contributor or admin may attach a resource to an artist.
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

        $artist = Artist::factory()->create();
        $resource = ExternalResource::factory()->create();
        $policy = new ArtistPolicy();

        $this->assertFalse($policy->attachExternalResource($viewer, $artist, $resource));
        $this->assertTrue($policy->attachExternalResource($editor, $artist, $resource));
        $this->assertTrue($policy->attachExternalResource($admin, $artist, $resource));
    }

    /**
     * A contributor or admin may detach a resource from an artist.
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

        $artist = Artist::factory()->create();
        $resource = ExternalResource::factory()->create();
        $policy = new ArtistPolicy();

        $this->assertFalse($policy->detachExternalResource($viewer, $artist, $resource));
        $this->assertTrue($policy->detachExternalResource($editor, $artist, $resource));
        $this->assertTrue($policy->detachExternalResource($admin, $artist, $resource));
    }

    /**
     * A contributor or admin may attach any song to an artist.
     *
     * @return void
     */
    public function testAttachAnySong()
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

        $artist = Artist::factory()->create();
        $policy = new ArtistPolicy();

        $this->assertFalse($policy->attachAnySong($viewer, $artist));
        $this->assertTrue($policy->attachAnySong($editor, $artist));
        $this->assertTrue($policy->attachAnySong($admin, $artist));
    }

    /**
     * A contributor or admin may attach a song to an artist.
     *
     * @return void
     */
    public function testAttachSong()
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

        $artist = Artist::factory()->create();
        $song = Song::factory()->create();
        $policy = new ArtistPolicy();

        $this->assertFalse($policy->attachSong($viewer, $artist, $song));
        $this->assertTrue($policy->attachSong($editor, $artist, $song));
        $this->assertTrue($policy->attachSong($admin, $artist, $song));
    }

    /**
     * A contributor or admin may detach a song from an artist.
     *
     * @return void
     */
    public function testDetachSong()
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

        $artist = Artist::factory()->create();
        $song = Song::factory()->create();
        $policy = new ArtistPolicy();

        $this->assertFalse($policy->detachSong($viewer, $artist, $song));
        $this->assertTrue($policy->detachSong($editor, $artist, $song));
        $this->assertTrue($policy->detachSong($admin, $artist, $song));
    }

    /**
     * A contributor or admin may attach any group/member to an artist.
     *
     * @return void
     */
    public function testAttachAnyArtist()
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

        $artist = Artist::factory()->create();
        $policy = new ArtistPolicy();

        $this->assertFalse($policy->attachAnyArtist($viewer, $artist));
        $this->assertTrue($policy->attachAnyArtist($editor, $artist));
        $this->assertTrue($policy->attachAnyArtist($admin, $artist));
    }

    /**
     * A contributor or admin may attach a group/member to an artist.
     *
     * @return void
     */
    public function testAttachArtist()
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

        $artist = Artist::factory()->create();
        $member_group = Artist::factory()->create();
        $policy = new ArtistPolicy();

        $this->assertFalse($policy->attachArtist($viewer, $artist, $member_group));
        $this->assertTrue($policy->attachArtist($editor, $artist, $member_group));
        $this->assertTrue($policy->attachArtist($admin, $artist, $member_group));
    }

    /**
     * A contributor or admin may detach a group/member from an artist.
     *
     * @return void
     */
    public function testDetachArtist()
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

        $artist = Artist::factory()->create();
        $group_member = Artist::factory()->create();
        $policy = new ArtistPolicy();

        $this->assertFalse($policy->detachArtist($viewer, $artist, $group_member));
        $this->assertTrue($policy->detachArtist($editor, $artist, $group_member));
        $this->assertTrue($policy->detachArtist($admin, $artist, $group_member));
    }

    /**
     * A contributor or admin may attach any image to an artist.
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

        $artist = Artist::factory()->create();
        $policy = new ArtistPolicy();

        $this->assertFalse($policy->attachAnyImage($viewer, $artist));
        $this->assertTrue($policy->attachAnyImage($editor, $artist));
        $this->assertTrue($policy->attachAnyImage($admin, $artist));
    }

    /**
     * A contributor or admin may attach an image to an artist if not already attached.
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

        $artist = Artist::factory()->create();
        $image = Image::factory()->create();
        $policy = new ArtistPolicy();

        $this->assertFalse($policy->attachImage($viewer, $artist, $image));
        $this->assertTrue($policy->attachImage($editor, $artist, $image));
        $this->assertTrue($policy->attachImage($admin, $artist, $image));
    }

    /**
     * If an image is already attached to an artist, no role may attach it.
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

        $artist = Artist::factory()->create();
        $image = Image::factory()->create();
        $artist->images()->attach($image);
        $policy = new ArtistPolicy();

        $this->assertFalse($policy->attachImage($viewer, $artist, $image));
        $this->assertFalse($policy->attachImage($editor, $artist, $image));
        $this->assertFalse($policy->attachImage($admin, $artist, $image));
    }

    /**
     * A contributor or admin may detach an image from an artist.
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

        $artist = Artist::factory()->create();
        $image = Image::factory()->create();
        $policy = new ArtistPolicy();

        $this->assertFalse($policy->detachImage($viewer, $artist, $image));
        $this->assertTrue($policy->detachImage($editor, $artist, $image));
        $this->assertTrue($policy->detachImage($admin, $artist, $image));
    }
}
