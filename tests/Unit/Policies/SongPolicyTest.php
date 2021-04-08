<?php

namespace Tests\Unit\Policies;

use App\Models\Artist;
use App\Models\Song;
use App\Models\User;
use App\Policies\SongPolicy;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithoutEvents;
use Tests\TestCase;

class SongPolicyTest extends TestCase
{
    use RefreshDatabase, WithoutEvents;

    /**
     * Any user regardless of role can view any song.
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

        $policy = new SongPolicy();

        $this->assertTrue($policy->viewAny($viewer));
        $this->assertTrue($policy->viewAny($editor));
        $this->assertTrue($policy->viewAny($admin));
    }

    /**
     * Any user regardless of role can view a song.
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

        $song = Song::factory()->create();
        $policy = new SongPolicy();

        $this->assertTrue($policy->view($viewer, $song));
        $this->assertTrue($policy->view($editor, $song));
        $this->assertTrue($policy->view($admin, $song));
    }

    /**
     * A contributor or admin may create a song.
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

        $policy = new SongPolicy();

        $this->assertFalse($policy->create($viewer));
        $this->assertTrue($policy->create($editor));
        $this->assertTrue($policy->create($admin));
    }

    /**
     * A contributor or admin may update a song.
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

        $song = Song::factory()->create();
        $policy = new SongPolicy();

        $this->assertFalse($policy->update($viewer, $song));
        $this->assertTrue($policy->update($editor, $song));
        $this->assertTrue($policy->update($admin, $song));
    }

    /**
     * A contributor or admin may delete a song.
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

        $song = Song::factory()->create();
        $policy = new SongPolicy();

        $this->assertFalse($policy->delete($viewer, $song));
        $this->assertTrue($policy->delete($editor, $song));
        $this->assertTrue($policy->delete($admin, $song));
    }

    /**
     * A contributor or admin may restore a song.
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

        $song = Song::factory()->create();
        $policy = new SongPolicy();

        $this->assertFalse($policy->restore($viewer, $song));
        $this->assertTrue($policy->restore($editor, $song));
        $this->assertTrue($policy->restore($admin, $song));
    }

    /**
     * A contributor or admin may force delete a song.
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

        $song = Song::factory()->create();
        $policy = new SongPolicy();

        $this->assertFalse($policy->forceDelete($viewer, $song));
        $this->assertFalse($policy->forceDelete($editor, $song));
        $this->assertTrue($policy->forceDelete($admin, $song));
    }

    /**
     * An admin can add a theme to a song.
     *
     * @return void
     */
    public function testAddTheme()
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

        $song = Song::factory()->create();
        $policy = new SongPolicy();

        $this->assertFalse($policy->addTheme($viewer, $song));
        $this->assertFalse($policy->addTheme($editor, $song));
        $this->assertTrue($policy->addTheme($admin, $song));
    }

    /**
     * A contributor or admin may attach any artist to a song.
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

        $song = Song::factory()->create();
        $policy = new SongPolicy();

        $this->assertFalse($policy->attachAnyArtist($viewer, $song));
        $this->assertTrue($policy->attachAnyArtist($editor, $song));
        $this->assertTrue($policy->attachAnyArtist($admin, $song));
    }

    /**
     * A contributor or admin may attach an artist to a song.
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

        $song = Song::factory()->create();
        $artist = Artist::factory()->create();
        $policy = new SongPolicy();

        $this->assertFalse($policy->attachArtist($viewer, $song, $artist));
        $this->assertTrue($policy->attachArtist($editor, $song, $artist));
        $this->assertTrue($policy->attachArtist($admin, $song, $artist));
    }

    /**
     * A contributor or admin may detach an artist from a song.
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

        $song = Song::factory()->create();
        $artist = Artist::factory()->create();
        $policy = new SongPolicy();

        $this->assertFalse($policy->detachArtist($viewer, $song, $artist));
        $this->assertTrue($policy->detachArtist($editor, $song, $artist));
        $this->assertTrue($policy->detachArtist($admin, $song, $artist));
    }
}
