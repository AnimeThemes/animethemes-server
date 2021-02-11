<?php

namespace Tests\Unit\Policies;

use App\Enums\UserRole;
use App\Models\Artist;
use App\Models\ExternalResource;
use App\Models\Image;
use App\Models\Song;
use App\Models\User;
use App\Policies\ArtistPolicy;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class ArtistPolicyTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    /**
     * Any user regardless of role can view any artist.
     *
     * @return void
     */
    public function testViewAny()
    {
        $read_only = User::factory()->create([
            'role' => UserRole::READ_ONLY,
        ]);

        $contributor = User::factory()->create([
            'role' => UserRole::CONTRIBUTOR,
        ]);

        $admin = User::factory()->create([
            'role' => UserRole::ADMIN,
        ]);

        $policy = new ArtistPolicy();

        $this->assertTrue($policy->viewAny($read_only));
        $this->assertTrue($policy->viewAny($contributor));
        $this->assertTrue($policy->viewAny($admin));
    }

    /**
     * Any user regardless of role can view an artist.
     *
     * @return void
     */
    public function testView()
    {
        $read_only = User::factory()->create([
            'role' => UserRole::READ_ONLY,
        ]);

        $contributor = User::factory()->create([
            'role' => UserRole::CONTRIBUTOR,
        ]);

        $admin = User::factory()->create([
            'role' => UserRole::ADMIN,
        ]);

        $artist = Artist::factory()->create();
        $policy = new ArtistPolicy();

        $this->assertTrue($policy->view($read_only, $artist));
        $this->assertTrue($policy->view($contributor, $artist));
        $this->assertTrue($policy->view($admin, $artist));
    }

    /**
     * A contributor or admin may create an artist.
     *
     * @return void
     */
    public function testCreate()
    {
        $read_only = User::factory()->create([
            'role' => UserRole::READ_ONLY,
        ]);

        $contributor = User::factory()->create([
            'role' => UserRole::CONTRIBUTOR,
        ]);

        $admin = User::factory()->create([
            'role' => UserRole::ADMIN,
        ]);

        $policy = new ArtistPolicy();

        $this->assertFalse($policy->create($read_only));
        $this->assertTrue($policy->create($contributor));
        $this->assertTrue($policy->create($admin));
    }

    /**
     * A contributor or admin may update an artist.
     *
     * @return void
     */
    public function testUpdate()
    {
        $read_only = User::factory()->create([
            'role' => UserRole::READ_ONLY,
        ]);

        $contributor = User::factory()->create([
            'role' => UserRole::CONTRIBUTOR,
        ]);

        $admin = User::factory()->create([
            'role' => UserRole::ADMIN,
        ]);

        $artist = Artist::factory()->create();
        $policy = new ArtistPolicy();

        $this->assertFalse($policy->update($read_only, $artist));
        $this->assertTrue($policy->update($contributor, $artist));
        $this->assertTrue($policy->update($admin, $artist));
    }

    /**
     * A contributor or admin may delete an artist.
     *
     * @return void
     */
    public function testDelete()
    {
        $read_only = User::factory()->create([
            'role' => UserRole::READ_ONLY,
        ]);

        $contributor = User::factory()->create([
            'role' => UserRole::CONTRIBUTOR,
        ]);

        $admin = User::factory()->create([
            'role' => UserRole::ADMIN,
        ]);

        $artist = Artist::factory()->create();
        $policy = new ArtistPolicy();

        $this->assertFalse($policy->delete($read_only, $artist));
        $this->assertTrue($policy->delete($contributor, $artist));
        $this->assertTrue($policy->delete($admin, $artist));
    }

    /**
     * A contributor or admin may restore an artist.
     *
     * @return void
     */
    public function testRestore()
    {
        $read_only = User::factory()->create([
            'role' => UserRole::READ_ONLY,
        ]);

        $contributor = User::factory()->create([
            'role' => UserRole::CONTRIBUTOR,
        ]);

        $admin = User::factory()->create([
            'role' => UserRole::ADMIN,
        ]);

        $artist = Artist::factory()->create();
        $policy = new ArtistPolicy();

        $this->assertFalse($policy->restore($read_only, $artist));
        $this->assertTrue($policy->restore($contributor, $artist));
        $this->assertTrue($policy->restore($admin, $artist));
    }

    /**
     * A contributor or admin may force delete an artist.
     *
     * @return void
     */
    public function testForceDelete()
    {
        $read_only = User::factory()->create([
            'role' => UserRole::READ_ONLY,
        ]);

        $contributor = User::factory()->create([
            'role' => UserRole::CONTRIBUTOR,
        ]);

        $admin = User::factory()->create([
            'role' => UserRole::ADMIN,
        ]);

        $artist = Artist::factory()->create();
        $policy = new ArtistPolicy();

        $this->assertFalse($policy->forceDelete($read_only, $artist));
        $this->assertTrue($policy->forceDelete($contributor, $artist));
        $this->assertTrue($policy->forceDelete($admin, $artist));
    }

    /**
     * A contributor or admin may attach any resource to an artist.
     *
     * @return void
     */
    public function testAttachAnyResource()
    {
        $read_only = User::factory()->create([
            'role' => UserRole::READ_ONLY,
        ]);

        $contributor = User::factory()->create([
            'role' => UserRole::CONTRIBUTOR,
        ]);

        $admin = User::factory()->create([
            'role' => UserRole::ADMIN,
        ]);

        $artist = Artist::factory()->create();
        $policy = new ArtistPolicy();

        $this->assertFalse($policy->attachAnyExternalResource($read_only, $artist));
        $this->assertTrue($policy->attachAnyExternalResource($contributor, $artist));
        $this->assertTrue($policy->attachAnyExternalResource($admin, $artist));
    }

    /**
     * A contributor or admin may attach a resource to an artist.
     *
     * @return void
     */
    public function testAttachResource()
    {
        $read_only = User::factory()->create([
            'role' => UserRole::READ_ONLY,
        ]);

        $contributor = User::factory()->create([
            'role' => UserRole::CONTRIBUTOR,
        ]);

        $admin = User::factory()->create([
            'role' => UserRole::ADMIN,
        ]);

        $artist = Artist::factory()->create();
        $resource = ExternalResource::factory()->create();
        $policy = new ArtistPolicy();

        $this->assertFalse($policy->attachExternalResource($read_only, $artist, $resource));
        $this->assertTrue($policy->attachExternalResource($contributor, $artist, $resource));
        $this->assertTrue($policy->attachExternalResource($admin, $artist, $resource));
    }

    /**
     * A contributor or admin may detach a resource from an artist.
     *
     * @return void
     */
    public function testDetachResource()
    {
        $read_only = User::factory()->create([
            'role' => UserRole::READ_ONLY,
        ]);

        $contributor = User::factory()->create([
            'role' => UserRole::CONTRIBUTOR,
        ]);

        $admin = User::factory()->create([
            'role' => UserRole::ADMIN,
        ]);

        $artist = Artist::factory()->create();
        $resource = ExternalResource::factory()->create();
        $policy = new ArtistPolicy();

        $this->assertFalse($policy->detachExternalResource($read_only, $artist, $resource));
        $this->assertTrue($policy->detachExternalResource($contributor, $artist, $resource));
        $this->assertTrue($policy->detachExternalResource($admin, $artist, $resource));
    }

    /**
     * A contributor or admin may attach any song to an artist.
     *
     * @return void
     */
    public function testAttachAnySong()
    {
        $read_only = User::factory()->create([
            'role' => UserRole::READ_ONLY,
        ]);

        $contributor = User::factory()->create([
            'role' => UserRole::CONTRIBUTOR,
        ]);

        $admin = User::factory()->create([
            'role' => UserRole::ADMIN,
        ]);

        $artist = Artist::factory()->create();
        $policy = new ArtistPolicy();

        $this->assertFalse($policy->attachAnySong($read_only, $artist));
        $this->assertTrue($policy->attachAnySong($contributor, $artist));
        $this->assertTrue($policy->attachAnySong($admin, $artist));
    }

    /**
     * A contributor or admin may attach a song to an artist.
     *
     * @return void
     */
    public function testAttachSong()
    {
        $read_only = User::factory()->create([
            'role' => UserRole::READ_ONLY,
        ]);

        $contributor = User::factory()->create([
            'role' => UserRole::CONTRIBUTOR,
        ]);

        $admin = User::factory()->create([
            'role' => UserRole::ADMIN,
        ]);

        $artist = Artist::factory()->create();
        $song = Song::factory()->create();
        $policy = new ArtistPolicy();

        $this->assertFalse($policy->attachSong($read_only, $artist, $song));
        $this->assertTrue($policy->attachSong($contributor, $artist, $song));
        $this->assertTrue($policy->attachSong($admin, $artist, $song));
    }

    /**
     * A contributor or admin may detach a song from an artist.
     *
     * @return void
     */
    public function testDetachSong()
    {
        $read_only = User::factory()->create([
            'role' => UserRole::READ_ONLY,
        ]);

        $contributor = User::factory()->create([
            'role' => UserRole::CONTRIBUTOR,
        ]);

        $admin = User::factory()->create([
            'role' => UserRole::ADMIN,
        ]);

        $artist = Artist::factory()->create();
        $song = Song::factory()->create();
        $policy = new ArtistPolicy();

        $this->assertFalse($policy->detachSong($read_only, $artist, $song));
        $this->assertTrue($policy->detachSong($contributor, $artist, $song));
        $this->assertTrue($policy->detachSong($admin, $artist, $song));
    }

    /**
     * A contributor or admin may attach any group/member to an artist.
     *
     * @return void
     */
    public function testAttachAnyArtist()
    {
        $read_only = User::factory()->create([
            'role' => UserRole::READ_ONLY,
        ]);

        $contributor = User::factory()->create([
            'role' => UserRole::CONTRIBUTOR,
        ]);

        $admin = User::factory()->create([
            'role' => UserRole::ADMIN,
        ]);

        $artist = Artist::factory()->create();
        $policy = new ArtistPolicy();

        $this->assertFalse($policy->attachAnyArtist($read_only, $artist));
        $this->assertTrue($policy->attachAnyArtist($contributor, $artist));
        $this->assertTrue($policy->attachAnyArtist($admin, $artist));
    }

    /**
     * A contributor or admin may attach a group/member to an artist.
     *
     * @return void
     */
    public function testAttachArtist()
    {
        $read_only = User::factory()->create([
            'role' => UserRole::READ_ONLY,
        ]);

        $contributor = User::factory()->create([
            'role' => UserRole::CONTRIBUTOR,
        ]);

        $admin = User::factory()->create([
            'role' => UserRole::ADMIN,
        ]);

        $artist = Artist::factory()->create();
        $member_group = Artist::factory()->create();
        $policy = new ArtistPolicy();

        $this->assertFalse($policy->attachArtist($read_only, $artist, $member_group));
        $this->assertTrue($policy->attachArtist($contributor, $artist, $member_group));
        $this->assertTrue($policy->attachArtist($admin, $artist, $member_group));
    }

    /**
     * A contributor or admin may detach a group/member from an artist.
     *
     * @return void
     */
    public function testDetachArtist()
    {
        $read_only = User::factory()->create([
            'role' => UserRole::READ_ONLY,
        ]);

        $contributor = User::factory()->create([
            'role' => UserRole::CONTRIBUTOR,
        ]);

        $admin = User::factory()->create([
            'role' => UserRole::ADMIN,
        ]);

        $artist = Artist::factory()->create();
        $group_member = Artist::factory()->create();
        $policy = new ArtistPolicy();

        $this->assertFalse($policy->detachArtist($read_only, $artist, $group_member));
        $this->assertTrue($policy->detachArtist($contributor, $artist, $group_member));
        $this->assertTrue($policy->detachArtist($admin, $artist, $group_member));
    }

    /**
     * A contributor or admin may attach any image to an artist.
     *
     * @return void
     */
    public function testAttachAnyImage()
    {
        $read_only = User::factory()->create([
            'role' => UserRole::READ_ONLY,
        ]);

        $contributor = User::factory()->create([
            'role' => UserRole::CONTRIBUTOR,
        ]);

        $admin = User::factory()->create([
            'role' => UserRole::ADMIN,
        ]);

        $artist = Artist::factory()->create();
        $policy = new ArtistPolicy();

        $this->assertFalse($policy->attachAnyImage($read_only, $artist));
        $this->assertTrue($policy->attachAnyImage($contributor, $artist));
        $this->assertTrue($policy->attachAnyImage($admin, $artist));
    }

    /**
     * A contributor or admin may attach an image to an artist if not already attached.
     *
     * @return void
     */
    public function testAttachNewImage()
    {
        $read_only = User::factory()->create([
            'role' => UserRole::READ_ONLY,
        ]);

        $contributor = User::factory()->create([
            'role' => UserRole::CONTRIBUTOR,
        ]);

        $admin = User::factory()->create([
            'role' => UserRole::ADMIN,
        ]);

        $artist = Artist::factory()->create();
        $image = Image::factory()->create();
        $policy = new ArtistPolicy();

        $this->assertFalse($policy->attachImage($read_only, $artist, $image));
        $this->assertTrue($policy->attachImage($contributor, $artist, $image));
        $this->assertTrue($policy->attachImage($admin, $artist, $image));
    }

    /**
     * If an image is already attached to an artist, no role may attach it.
     *
     * @return void
     */
    public function testAttachExistingImage()
    {
        $read_only = User::factory()->create([
            'role' => UserRole::READ_ONLY,
        ]);

        $contributor = User::factory()->create([
            'role' => UserRole::CONTRIBUTOR,
        ]);

        $admin = User::factory()->create([
            'role' => UserRole::ADMIN,
        ]);

        $artist = Artist::factory()->create();
        $image = Image::factory()->create();
        $artist->images()->attach($image);
        $policy = new ArtistPolicy();

        $this->assertFalse($policy->attachImage($read_only, $artist, $image));
        $this->assertFalse($policy->attachImage($contributor, $artist, $image));
        $this->assertFalse($policy->attachImage($admin, $artist, $image));
    }

    /**
     * A contributor or admin may detach an image from an artist.
     *
     * @return void
     */
    public function testDetachImage()
    {
        $read_only = User::factory()->create([
            'role' => UserRole::READ_ONLY,
        ]);

        $contributor = User::factory()->create([
            'role' => UserRole::CONTRIBUTOR,
        ]);

        $admin = User::factory()->create([
            'role' => UserRole::ADMIN,
        ]);

        $artist = Artist::factory()->create();
        $image = Image::factory()->create();
        $policy = new ArtistPolicy();

        $this->assertFalse($policy->detachImage($read_only, $artist, $image));
        $this->assertTrue($policy->detachImage($contributor, $artist, $image));
        $this->assertTrue($policy->detachImage($admin, $artist, $image));
    }
}
