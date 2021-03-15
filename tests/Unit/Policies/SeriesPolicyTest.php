<?php

namespace Tests\Unit\Policies;

use App\Enums\UserRole;
use App\Models\Anime;
use App\Models\Series;
use App\Models\User;
use App\Policies\SeriesPolicy;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithoutEvents;
use Tests\TestCase;

class SeriesPolicyTest extends TestCase
{
    use RefreshDatabase, WithoutEvents;

    /**
     * Any user regardless of role can view any series.
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

        $policy = new SeriesPolicy();

        $this->assertTrue($policy->viewAny($read_only));
        $this->assertTrue($policy->viewAny($contributor));
        $this->assertTrue($policy->viewAny($admin));
    }

    /**
     * Any user regardless of role can view a series.
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

        $series = Series::factory()->create();
        $policy = new SeriesPolicy();

        $this->assertTrue($policy->view($read_only, $series));
        $this->assertTrue($policy->view($contributor, $series));
        $this->assertTrue($policy->view($admin, $series));
    }

    /**
     * A contributor or admin may create a series.
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

        $policy = new SeriesPolicy();

        $this->assertFalse($policy->create($read_only));
        $this->assertTrue($policy->create($contributor));
        $this->assertTrue($policy->create($admin));
    }

    /**
     * A contributor or admin may update a series.
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

        $series = Series::factory()->create();
        $policy = new SeriesPolicy();

        $this->assertFalse($policy->update($read_only, $series));
        $this->assertTrue($policy->update($contributor, $series));
        $this->assertTrue($policy->update($admin, $series));
    }

    /**
     * A contributor or admin may delete a series.
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

        $series = Series::factory()->create();
        $policy = new SeriesPolicy();

        $this->assertFalse($policy->delete($read_only, $series));
        $this->assertTrue($policy->delete($contributor, $series));
        $this->assertTrue($policy->delete($admin, $series));
    }

    /**
     * A contributor or admin may restore a series.
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

        $series = Series::factory()->create();
        $policy = new SeriesPolicy();

        $this->assertFalse($policy->restore($read_only, $series));
        $this->assertTrue($policy->restore($contributor, $series));
        $this->assertTrue($policy->restore($admin, $series));
    }

    /**
     * A contributor or admin may force delete a series.
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

        $series = Series::factory()->create();
        $policy = new SeriesPolicy();

        $this->assertFalse($policy->forceDelete($read_only, $series));
        $this->assertFalse($policy->forceDelete($contributor, $series));
        $this->assertTrue($policy->forceDelete($admin, $series));
    }

    /**
     * A contributor or admin may attach any anime to a series.
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

        $series = Series::factory()->create();
        $policy = new SeriesPolicy();

        $this->assertFalse($policy->attachAnyAnime($read_only, $series));
        $this->assertTrue($policy->attachAnyAnime($contributor, $series));
        $this->assertTrue($policy->attachAnyAnime($admin, $series));
    }

    /**
     * A contributor or admin may attach an anime to a series.
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

        $series = Series::factory()->create();
        $anime = Anime::factory()->create();
        $policy = new SeriesPolicy();

        $this->assertFalse($policy->attachAnime($read_only, $series, $anime));
        $this->assertTrue($policy->attachAnime($contributor, $series, $anime));
        $this->assertTrue($policy->attachAnime($admin, $series, $anime));
    }

    /**
     * A contributor or admin may detach an anime from a series.
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

        $series = Series::factory()->create();
        $anime = Anime::factory()->create();
        $policy = new SeriesPolicy();

        $this->assertFalse($policy->detachAnime($read_only, $series, $anime));
        $this->assertTrue($policy->detachAnime($contributor, $series, $anime));
        $this->assertTrue($policy->detachAnime($admin, $series, $anime));
    }
}
