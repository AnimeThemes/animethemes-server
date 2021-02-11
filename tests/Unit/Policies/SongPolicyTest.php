<?php

namespace Tests\Unit\Policies;

use App\Enums\UserRole;
use App\Models\Artist;
use App\Models\Song;
use App\Models\User;
use App\Policies\SongPolicy;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class SongPolicyTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    /**
     * Any user regardless of role can view any song.
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

        $policy = new SongPolicy();

        $this->assertTrue($policy->viewAny($read_only));
        $this->assertTrue($policy->viewAny($contributor));
        $this->assertTrue($policy->viewAny($admin));
    }

    /**
     * Any user regardless of role can view a song.
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

        $song = Song::factory()->create();
        $policy = new SongPolicy();

        $this->assertTrue($policy->view($read_only, $song));
        $this->assertTrue($policy->view($contributor, $song));
        $this->assertTrue($policy->view($admin, $song));
    }

    /**
     * A contributor or admin may create a song.
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

        $policy = new SongPolicy();

        $this->assertFalse($policy->create($read_only));
        $this->assertTrue($policy->create($contributor));
        $this->assertTrue($policy->create($admin));
    }

    /**
     * A contributor or admin may update a song.
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

        $song = Song::factory()->create();
        $policy = new SongPolicy();

        $this->assertFalse($policy->update($read_only, $song));
        $this->assertTrue($policy->update($contributor, $song));
        $this->assertTrue($policy->update($admin, $song));
    }

    /**
     * A contributor or admin may delete a song.
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

        $song = Song::factory()->create();
        $policy = new SongPolicy();

        $this->assertFalse($policy->delete($read_only, $song));
        $this->assertTrue($policy->delete($contributor, $song));
        $this->assertTrue($policy->delete($admin, $song));
    }

    /**
     * A contributor or admin may restore a song.
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

        $song = Song::factory()->create();
        $policy = new SongPolicy();

        $this->assertFalse($policy->restore($read_only, $song));
        $this->assertTrue($policy->restore($contributor, $song));
        $this->assertTrue($policy->restore($admin, $song));
    }

    /**
     * A contributor or admin may force delete a song.
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

        $song = Song::factory()->create();
        $policy = new SongPolicy();

        $this->assertFalse($policy->forceDelete($read_only, $song));
        $this->assertTrue($policy->forceDelete($contributor, $song));
        $this->assertTrue($policy->forceDelete($admin, $song));
    }

    /**
     * An admin can add a theme to a song.
     *
     * @return void
     */
    public function testAddTheme()
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

        $song = Song::factory()->create();
        $policy = new SongPolicy();

        $this->assertFalse($policy->addTheme($read_only, $song));
        $this->assertFalse($policy->addTheme($contributor, $song));
        $this->assertTrue($policy->addTheme($admin, $song));
    }

    /**
     * A contributor or admin may attach any artist to a song.
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

        $song = Song::factory()->create();
        $policy = new SongPolicy();

        $this->assertFalse($policy->attachAnyArtist($read_only, $song));
        $this->assertTrue($policy->attachAnyArtist($contributor, $song));
        $this->assertTrue($policy->attachAnyArtist($admin, $song));
    }

    /**
     * A contributor or admin may attach an artist to a song.
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

        $song = Song::factory()->create();
        $artist = Artist::factory()->create();
        $policy = new SongPolicy();

        $this->assertFalse($policy->attachArtist($read_only, $song, $artist));
        $this->assertTrue($policy->attachArtist($contributor, $song, $artist));
        $this->assertTrue($policy->attachArtist($admin, $song, $artist));
    }

    /**
     * A contributor or admin may detach an artist from a song.
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

        $song = Song::factory()->create();
        $artist = Artist::factory()->create();
        $policy = new SongPolicy();

        $this->assertFalse($policy->detachArtist($read_only, $song, $artist));
        $this->assertTrue($policy->detachArtist($contributor, $song, $artist));
        $this->assertTrue($policy->detachArtist($admin, $song, $artist));
    }
}
