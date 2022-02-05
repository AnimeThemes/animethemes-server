<?php

declare(strict_types=1);

namespace Tests\Unit\Policies\Wiki;

use App\Models\Auth\User;
use App\Models\Wiki\Anime;
use App\Models\Wiki\Series;
use App\Policies\Wiki\SeriesPolicy;
use Illuminate\Foundation\Testing\WithoutEvents;
use Tests\TestCase;

/**
 * Class SeriesPolicyTest.
 */
class SeriesPolicyTest extends TestCase
{
    use WithoutEvents;

    /**
     * Any user regardless of role can view any series.
     *
     * @return void
     */
    public function testViewAny(): void
    {
        $policy = new SeriesPolicy();

        static::assertTrue($policy->viewAny());
    }

    /**
     * Any user regardless of role can view a series.
     *
     * @return void
     */
    public function testView(): void
    {
        $policy = new SeriesPolicy();

        static::assertTrue($policy->view());
    }

    /**
     * A contributor or admin may create a series.
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

        $policy = new SeriesPolicy();

        static::assertFalse($policy->create($viewer));
        static::assertTrue($policy->create($editor));
        static::assertTrue($policy->create($admin));
    }

    /**
     * A contributor or admin may update a series.
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

        $policy = new SeriesPolicy();

        static::assertFalse($policy->update($viewer));
        static::assertTrue($policy->update($editor));
        static::assertTrue($policy->update($admin));
    }

    /**
     * A contributor or admin may delete a series.
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

        $policy = new SeriesPolicy();

        static::assertFalse($policy->delete($viewer));
        static::assertTrue($policy->delete($editor));
        static::assertTrue($policy->delete($admin));
    }

    /**
     * A contributor or admin may restore a series.
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

        $policy = new SeriesPolicy();

        static::assertFalse($policy->restore($viewer));
        static::assertTrue($policy->restore($editor));
        static::assertTrue($policy->restore($admin));
    }

    /**
     * A contributor or admin may force delete a series.
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

        $policy = new SeriesPolicy();

        static::assertFalse($policy->forceDelete($viewer));
        static::assertFalse($policy->forceDelete($editor));
        static::assertTrue($policy->forceDelete($admin));
    }

    /**
     * A contributor or admin may attach any anime to a series.
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

        $policy = new SeriesPolicy();

        static::assertFalse($policy->attachAnyAnime($viewer));
        static::assertTrue($policy->attachAnyAnime($editor));
        static::assertTrue($policy->attachAnyAnime($admin));
    }

    /**
     * A contributor or admin may attach a series to an anime if not already attached.
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

        $series = Series::factory()->createOne();
        $anime = Anime::factory()->createOne();
        $policy = new SeriesPolicy();

        static::assertFalse($policy->attachAnime($viewer, $series, $anime));
        static::assertTrue($policy->attachAnime($editor, $series, $anime));
        static::assertTrue($policy->attachAnime($admin, $series, $anime));
    }

    /**
     * If a series is already attached to an anime, no role may attach it.
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

        $series = Series::factory()->createOne();
        $anime = Anime::factory()->createOne();
        $anime->series()->attach($series);
        $policy = new SeriesPolicy();

        static::assertFalse($policy->attachAnime($viewer, $series, $anime));
        static::assertFalse($policy->attachAnime($editor, $series, $anime));
        static::assertFalse($policy->attachAnime($admin, $series, $anime));
    }

    /**
     * A contributor or admin may detach an anime from a series.
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

        $policy = new SeriesPolicy();

        static::assertFalse($policy->detachAnime($viewer));
        static::assertTrue($policy->detachAnime($editor));
        static::assertTrue($policy->detachAnime($admin));
    }
}
