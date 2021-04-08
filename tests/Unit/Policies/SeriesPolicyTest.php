<?php

namespace Tests\Unit\Policies;

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
        $viewer = User::factory()
            ->withCurrentTeam('viewer')
            ->create();

        $editor = User::factory()
            ->withCurrentTeam('editor')
            ->create();

        $admin = User::factory()
            ->withCurrentTeam('admin')
            ->create();

        $policy = new SeriesPolicy();

        $this->assertTrue($policy->viewAny($viewer));
        $this->assertTrue($policy->viewAny($editor));
        $this->assertTrue($policy->viewAny($admin));
    }

    /**
     * Any user regardless of role can view a series.
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

        $series = Series::factory()->create();
        $policy = new SeriesPolicy();

        $this->assertTrue($policy->view($viewer, $series));
        $this->assertTrue($policy->view($editor, $series));
        $this->assertTrue($policy->view($admin, $series));
    }

    /**
     * A contributor or admin may create a series.
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

        $policy = new SeriesPolicy();

        $this->assertFalse($policy->create($viewer));
        $this->assertTrue($policy->create($editor));
        $this->assertTrue($policy->create($admin));
    }

    /**
     * A contributor or admin may update a series.
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

        $series = Series::factory()->create();
        $policy = new SeriesPolicy();

        $this->assertFalse($policy->update($viewer, $series));
        $this->assertTrue($policy->update($editor, $series));
        $this->assertTrue($policy->update($admin, $series));
    }

    /**
     * A contributor or admin may delete a series.
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

        $series = Series::factory()->create();
        $policy = new SeriesPolicy();

        $this->assertFalse($policy->delete($viewer, $series));
        $this->assertTrue($policy->delete($editor, $series));
        $this->assertTrue($policy->delete($admin, $series));
    }

    /**
     * A contributor or admin may restore a series.
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

        $series = Series::factory()->create();
        $policy = new SeriesPolicy();

        $this->assertFalse($policy->restore($viewer, $series));
        $this->assertTrue($policy->restore($editor, $series));
        $this->assertTrue($policy->restore($admin, $series));
    }

    /**
     * A contributor or admin may force delete a series.
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

        $series = Series::factory()->create();
        $policy = new SeriesPolicy();

        $this->assertFalse($policy->forceDelete($viewer, $series));
        $this->assertFalse($policy->forceDelete($editor, $series));
        $this->assertTrue($policy->forceDelete($admin, $series));
    }

    /**
     * A contributor or admin may attach any anime to a series.
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

        $series = Series::factory()->create();
        $policy = new SeriesPolicy();

        $this->assertFalse($policy->attachAnyAnime($viewer, $series));
        $this->assertTrue($policy->attachAnyAnime($editor, $series));
        $this->assertTrue($policy->attachAnyAnime($admin, $series));
    }

    /**
     * A contributor or admin may attach an anime to a series.
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

        $series = Series::factory()->create();
        $anime = Anime::factory()->create();
        $policy = new SeriesPolicy();

        $this->assertFalse($policy->attachAnime($viewer, $series, $anime));
        $this->assertTrue($policy->attachAnime($editor, $series, $anime));
        $this->assertTrue($policy->attachAnime($admin, $series, $anime));
    }

    /**
     * A contributor or admin may detach an anime from a series.
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

        $series = Series::factory()->create();
        $anime = Anime::factory()->create();
        $policy = new SeriesPolicy();

        $this->assertFalse($policy->detachAnime($viewer, $series, $anime));
        $this->assertTrue($policy->detachAnime($editor, $series, $anime));
        $this->assertTrue($policy->detachAnime($admin, $series, $anime));
    }
}
