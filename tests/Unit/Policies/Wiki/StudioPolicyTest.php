<?php

declare(strict_types=1);

namespace Tests\Unit\Policies\Wiki;

use App\Models\Auth\User;
use App\Models\Wiki\Anime;
use App\Models\Wiki\Studio;
use App\Policies\Wiki\StudioPolicy;
use Illuminate\Foundation\Testing\WithoutEvents;
use Tests\TestCase;

/**
 * Class StudioPolicyTest.
 */
class StudioPolicyTest extends TestCase
{
    use WithoutEvents;

    /**
     * Any user regardless of role can view any studio.
     *
     * @return void
     */
    public function testViewAny()
    {
        $policy = new StudioPolicy();

        static::assertTrue($policy->viewAny());
    }

    /**
     * Any user regardless of role can view a studio.
     *
     * @return void
     */
    public function testView()
    {
        $policy = new StudioPolicy();

        static::assertTrue($policy->view());
    }

    /**
     * A contributor or admin may create a studio.
     *
     * @return void
     */
    public function testCreate()
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

        $policy = new StudioPolicy();

        static::assertFalse($policy->create($viewer));
        static::assertTrue($policy->create($editor));
        static::assertTrue($policy->create($admin));
    }

    /**
     * A contributor or admin may update a studio.
     *
     * @return void
     */
    public function testUpdate()
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

        $policy = new StudioPolicy();

        static::assertFalse($policy->update($viewer));
        static::assertTrue($policy->update($editor));
        static::assertTrue($policy->update($admin));
    }

    /**
     * A contributor or admin may delete a studio.
     *
     * @return void
     */
    public function testDelete()
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

        $policy = new StudioPolicy();

        static::assertFalse($policy->delete($viewer));
        static::assertTrue($policy->delete($editor));
        static::assertTrue($policy->delete($admin));
    }

    /**
     * A contributor or admin may restore a studio.
     *
     * @return void
     */
    public function testRestore()
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

        $policy = new StudioPolicy();

        static::assertFalse($policy->restore($viewer));
        static::assertTrue($policy->restore($editor));
        static::assertTrue($policy->restore($admin));
    }

    /**
     * A contributor or admin may force delete a studio.
     *
     * @return void
     */
    public function testForceDelete()
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

        $policy = new StudioPolicy();

        static::assertFalse($policy->forceDelete($viewer));
        static::assertFalse($policy->forceDelete($editor));
        static::assertTrue($policy->forceDelete($admin));
    }

    /**
     * A contributor or admin may attach any anime to a studio.
     *
     * @return void
     */
    public function testAttachAnyAnime()
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

        $policy = new StudioPolicy();

        static::assertFalse($policy->attachAnyAnime($viewer));
        static::assertTrue($policy->attachAnyAnime($editor));
        static::assertTrue($policy->attachAnyAnime($admin));
    }

    /**
     * A contributor or admin may attach a series to an anime if not already attached.
     *
     * @return void
     */
    public function testAttachNewAnime()
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

        $studio = Studio::factory()->createOne();
        $anime = Anime::factory()->createOne();
        $policy = new StudioPolicy();

        static::assertFalse($policy->attachAnime($viewer, $studio, $anime));
        static::assertTrue($policy->attachAnime($editor, $studio, $anime));
        static::assertTrue($policy->attachAnime($admin, $studio, $anime));
    }

    /**
     * If a studio is already attached to an anime, no role may attach it.
     *
     * @return void
     */
    public function testAttachExistingAnime()
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

        $studio = Studio::factory()->createOne();
        $anime = Anime::factory()->createOne();
        $anime->studios()->attach($studio);
        $policy = new StudioPolicy();

        static::assertFalse($policy->attachAnime($viewer, $studio, $anime));
        static::assertFalse($policy->attachAnime($editor, $studio, $anime));
        static::assertFalse($policy->attachAnime($admin, $studio, $anime));
    }

    /**
     * A contributor or admin may detach an anime from a studio.
     *
     * @return void
     */
    public function testDetachAnime()
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

        $policy = new StudioPolicy();

        static::assertFalse($policy->detachAnime($viewer));
        static::assertTrue($policy->detachAnime($editor));
        static::assertTrue($policy->detachAnime($admin));
    }

    /**
     * A contributor or admin may attach any resource to a studio.
     * 
     * @return void
     */
    public function testAttachAnyResource()
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

        $policy = new StudioPolicy();

        static::assertFalse($policy->attachAnyExternalResource($viewer));
        static::assertTrue($policy->attachAnyExternalResource($editor));
        static::assertTrue($policy->attachAnyExternalResource($admin));
    }

    /**
     * A contributor or admin may attach a resource to a studio.
     * 
     * @return void
     */
    public function testAttachResource()
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

        $policy = new StudioPolicy();

        static::assertFalse($policy->attachExternalResource($viewer));
        static::assertTrue($policy->attachExternalResource($editor));
        static::assertTrue($policy->attachExternalResource($admin));
    }

    /**
     * A contributor or admin may detach a resource from a studio.
     * 
     * @return void
     */
    public function testDetachResource()
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

        $policy = new StudioPolicy();

        static::assertFalse($policy->detachExternalResource($viewer));
        static::assertTrue($policy->detachExternalResource($editor));
        static::assertTrue($policy->detachExternalResource($admin)); 
    }
}
