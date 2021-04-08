<?php

namespace Tests\Unit\Policies;

use App\Models\Anime;
use App\Models\Artist;
use App\Models\ExternalResource;
use App\Models\User;
use App\Policies\ExternalResourcePolicy;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithoutEvents;
use Tests\TestCase;

class ExternalResourcePolicyTest extends TestCase
{
    use RefreshDatabase, WithoutEvents;

    /**
     * Any user regardless of role can view any resource.
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

        $policy = new ExternalResourcePolicy();

        $this->assertTrue($policy->viewAny($viewer));
        $this->assertTrue($policy->viewAny($editor));
        $this->assertTrue($policy->viewAny($admin));
    }

    /**
     * Any user regardless of role can view a resource.
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

        $resource = ExternalResource::factory()->create();
        $policy = new ExternalResourcePolicy();

        $this->assertTrue($policy->view($viewer, $resource));
        $this->assertTrue($policy->view($editor, $resource));
        $this->assertTrue($policy->view($admin, $resource));
    }

    /**
     * A contributor or admin may create a resource.
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

        $policy = new ExternalResourcePolicy();

        $this->assertFalse($policy->create($viewer));
        $this->assertTrue($policy->create($editor));
        $this->assertTrue($policy->create($admin));
    }

    /**
     * A contributor or admin may update a resource.
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

        $resource = ExternalResource::factory()->create();
        $policy = new ExternalResourcePolicy();

        $this->assertFalse($policy->update($viewer, $resource));
        $this->assertTrue($policy->update($editor, $resource));
        $this->assertTrue($policy->update($admin, $resource));
    }

    /**
     * A contributor or admin may delete a resource.
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

        $resource = ExternalResource::factory()->create();
        $policy = new ExternalResourcePolicy();

        $this->assertFalse($policy->delete($viewer, $resource));
        $this->assertTrue($policy->delete($editor, $resource));
        $this->assertTrue($policy->delete($admin, $resource));
    }

    /**
     * A contributor or admin may restore a resource.
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

        $resource = ExternalResource::factory()->create();
        $policy = new ExternalResourcePolicy();

        $this->assertFalse($policy->restore($viewer, $resource));
        $this->assertTrue($policy->restore($editor, $resource));
        $this->assertTrue($policy->restore($admin, $resource));
    }

    /**
     * A contributor or admin may force delete a resource.
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

        $resource = ExternalResource::factory()->create();
        $policy = new ExternalResourcePolicy();

        $this->assertFalse($policy->forceDelete($viewer, $resource));
        $this->assertFalse($policy->forceDelete($editor, $resource));
        $this->assertTrue($policy->forceDelete($admin, $resource));
    }

    /**
     * A contributor or admin may attach any artist to a resource.
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

        $resource = ExternalResource::factory()->create();
        $policy = new ExternalResourcePolicy();

        $this->assertFalse($policy->attachAnyArtist($viewer, $resource));
        $this->assertTrue($policy->attachAnyArtist($editor, $resource));
        $this->assertTrue($policy->attachAnyArtist($admin, $resource));
    }

    /**
     * A contributor or admin may attach an artist to a resource.
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

        $resource = ExternalResource::factory()->create();
        $artist = Artist::factory()->create();
        $policy = new ExternalResourcePolicy();

        $this->assertFalse($policy->attachArtist($viewer, $resource, $artist));
        $this->assertTrue($policy->attachArtist($editor, $resource, $artist));
        $this->assertTrue($policy->attachArtist($admin, $resource, $artist));
    }

    /**
     * A contributor or admin may detach an artist from a resource.
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

        $resource = ExternalResource::factory()->create();
        $artist = Artist::factory()->create();
        $policy = new ExternalResourcePolicy();

        $this->assertFalse($policy->detachArtist($viewer, $resource, $artist));
        $this->assertTrue($policy->detachArtist($editor, $resource, $artist));
        $this->assertTrue($policy->detachArtist($admin, $resource, $artist));
    }

    /**
     * A contributor or admin may attach any anime to a resource.
     *
     * @return void
     */
    public function testAttachAnyAnime()
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

        $resource = ExternalResource::factory()->create();
        $policy = new ExternalResourcePolicy();

        $this->assertFalse($policy->attachAnyAnime($viewer, $resource));
        $this->assertTrue($policy->attachAnyAnime($editor, $resource));
        $this->assertTrue($policy->attachAnyAnime($admin, $resource));
    }

    /**
     * A contributor or admin may attach an anime to a resource.
     *
     * @return void
     */
    public function testAttachAnime()
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

        $resource = ExternalResource::factory()->create();
        $anime = Anime::factory()->create();
        $policy = new ExternalResourcePolicy();

        $this->assertFalse($policy->attachAnime($viewer, $resource, $anime));
        $this->assertTrue($policy->attachAnime($editor, $resource, $anime));
        $this->assertTrue($policy->attachAnime($admin, $resource, $anime));
    }

    /**
     * A contributor or admin may detach an anime from a resource.
     *
     * @return void
     */
    public function testDetachAnime()
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

        $resource = ExternalResource::factory()->create();
        $anime = Anime::factory()->create();
        $policy = new ExternalResourcePolicy();

        $this->assertFalse($policy->detachAnime($viewer, $resource, $anime));
        $this->assertTrue($policy->detachAnime($editor, $resource, $anime));
        $this->assertTrue($policy->detachAnime($admin, $resource, $anime));
    }
}
