<?php

namespace Tests\Unit\Policies;

use App\Enums\UserRole;
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
        $read_only = User::factory()->create([
            'role' => UserRole::READ_ONLY,
        ]);

        $contributor = User::factory()->create([
            'role' => UserRole::CONTRIBUTOR,
        ]);

        $admin = User::factory()->create([
            'role' => UserRole::ADMIN,
        ]);

        $policy = new ExternalResourcePolicy();

        $this->assertTrue($policy->viewAny($read_only));
        $this->assertTrue($policy->viewAny($contributor));
        $this->assertTrue($policy->viewAny($admin));
    }

    /**
     * Any user regardless of role can view a resource.
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

        $resource = ExternalResource::factory()->create();
        $policy = new ExternalResourcePolicy();

        $this->assertTrue($policy->view($read_only, $resource));
        $this->assertTrue($policy->view($contributor, $resource));
        $this->assertTrue($policy->view($admin, $resource));
    }

    /**
     * A contributor or admin may create a resource.
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

        $policy = new ExternalResourcePolicy();

        $this->assertFalse($policy->create($read_only));
        $this->assertTrue($policy->create($contributor));
        $this->assertTrue($policy->create($admin));
    }

    /**
     * A contributor or admin may update a resource.
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

        $resource = ExternalResource::factory()->create();
        $policy = new ExternalResourcePolicy();

        $this->assertFalse($policy->update($read_only, $resource));
        $this->assertTrue($policy->update($contributor, $resource));
        $this->assertTrue($policy->update($admin, $resource));
    }

    /**
     * A contributor or admin may delete a resource.
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

        $resource = ExternalResource::factory()->create();
        $policy = new ExternalResourcePolicy();

        $this->assertFalse($policy->delete($read_only, $resource));
        $this->assertTrue($policy->delete($contributor, $resource));
        $this->assertTrue($policy->delete($admin, $resource));
    }

    /**
     * A contributor or admin may restore a resource.
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

        $resource = ExternalResource::factory()->create();
        $policy = new ExternalResourcePolicy();

        $this->assertFalse($policy->restore($read_only, $resource));
        $this->assertTrue($policy->restore($contributor, $resource));
        $this->assertTrue($policy->restore($admin, $resource));
    }

    /**
     * A contributor or admin may force delete a resource.
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

        $resource = ExternalResource::factory()->create();
        $policy = new ExternalResourcePolicy();

        $this->assertFalse($policy->forceDelete($read_only, $resource));
        $this->assertFalse($policy->forceDelete($contributor, $resource));
        $this->assertTrue($policy->forceDelete($admin, $resource));
    }

    /**
     * A contributor or admin may attach any artist to a resource.
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

        $resource = ExternalResource::factory()->create();
        $policy = new ExternalResourcePolicy();

        $this->assertFalse($policy->attachAnyArtist($read_only, $resource));
        $this->assertTrue($policy->attachAnyArtist($contributor, $resource));
        $this->assertTrue($policy->attachAnyArtist($admin, $resource));
    }

    /**
     * A contributor or admin may attach an artist to a resource.
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

        $resource = ExternalResource::factory()->create();
        $artist = Artist::factory()->create();
        $policy = new ExternalResourcePolicy();

        $this->assertFalse($policy->attachArtist($read_only, $resource, $artist));
        $this->assertTrue($policy->attachArtist($contributor, $resource, $artist));
        $this->assertTrue($policy->attachArtist($admin, $resource, $artist));
    }

    /**
     * A contributor or admin may detach an artist from a resource.
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

        $resource = ExternalResource::factory()->create();
        $artist = Artist::factory()->create();
        $policy = new ExternalResourcePolicy();

        $this->assertFalse($policy->detachArtist($read_only, $resource, $artist));
        $this->assertTrue($policy->detachArtist($contributor, $resource, $artist));
        $this->assertTrue($policy->detachArtist($admin, $resource, $artist));
    }

    /**
     * A contributor or admin may attach any anime to a resource.
     *
     * @return void
     */
    public function testAttachAnyAnime()
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

        $resource = ExternalResource::factory()->create();
        $policy = new ExternalResourcePolicy();

        $this->assertFalse($policy->attachAnyAnime($read_only, $resource));
        $this->assertTrue($policy->attachAnyAnime($contributor, $resource));
        $this->assertTrue($policy->attachAnyAnime($admin, $resource));
    }

    /**
     * A contributor or admin may attach an anime to a resource.
     *
     * @return void
     */
    public function testAttachAnime()
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

        $resource = ExternalResource::factory()->create();
        $anime = Anime::factory()->create();
        $policy = new ExternalResourcePolicy();

        $this->assertFalse($policy->attachAnime($read_only, $resource, $anime));
        $this->assertTrue($policy->attachAnime($contributor, $resource, $anime));
        $this->assertTrue($policy->attachAnime($admin, $resource, $anime));
    }

    /**
     * A contributor or admin may detach an anime from a resource.
     *
     * @return void
     */
    public function testDetachAnime()
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

        $resource = ExternalResource::factory()->create();
        $anime = Anime::factory()->create();
        $policy = new ExternalResourcePolicy();

        $this->assertFalse($policy->detachAnime($read_only, $resource, $anime));
        $this->assertTrue($policy->detachAnime($contributor, $resource, $anime));
        $this->assertTrue($policy->detachAnime($admin, $resource, $anime));
    }
}
