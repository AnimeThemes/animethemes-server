<?php

declare(strict_types=1);

namespace Tests\Unit\Policies\Wiki;

use App\Models\Auth\User;
use App\Policies\Wiki\VideoPolicy;
use Tests\TestCase;

/**
 * Class VideoPolicyTest.
 */
class VideoPolicyTest extends TestCase
{
    /**
     * Any user regardless of role can view any video.
     *
     * @return void
     */
    public function testViewAny(): void
    {
        $policy = new VideoPolicy();

        static::assertTrue($policy->viewAny());
    }

    /**
     * Any user regardless of role can view a video.
     *
     * @return void
     */
    public function testView(): void
    {
        $policy = new VideoPolicy();

        static::assertTrue($policy->view());
    }

    /**
     * A contributor or admin may create a video.
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

        $policy = new VideoPolicy();

        static::assertFalse($policy->create($viewer));
        static::assertFalse($policy->create($editor));
        static::assertTrue($policy->create($admin));
    }

    /**
     * A contributor or admin may update a video.
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

        $policy = new VideoPolicy();

        static::assertFalse($policy->update($viewer));
        static::assertTrue($policy->update($editor));
        static::assertTrue($policy->update($admin));
    }

    /**
     * A contributor or admin may delete a video.
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

        $policy = new VideoPolicy();

        static::assertFalse($policy->delete($viewer));
        static::assertFalse($policy->delete($editor));
        static::assertTrue($policy->delete($admin));
    }

    /**
     * A contributor or admin may restore a video.
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

        $policy = new VideoPolicy();

        static::assertFalse($policy->restore($viewer));
        static::assertFalse($policy->restore($editor));
        static::assertTrue($policy->restore($admin));
    }

    /**
     * A contributor or admin may force delete a video.
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

        $policy = new VideoPolicy();

        static::assertFalse($policy->forceDelete($viewer));
        static::assertFalse($policy->forceDelete($editor));
        static::assertTrue($policy->forceDelete($admin));
    }

    /**
     * A contributor or admin may attach any entry to a video.
     *
     * @return void
     */
    public function testAttachAnyEntry(): void
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

        $policy = new VideoPolicy();

        static::assertFalse($policy->attachAnyAnimeThemeEntry($viewer));
        static::assertTrue($policy->attachAnyAnimeThemeEntry($editor));
        static::assertTrue($policy->attachAnyAnimeThemeEntry($admin));
    }

    /**
     * A contributor or admin may attach an entry to a video if not already attached.
     *
     * @return void
     */
    public function testAttachEntry(): void
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

        $policy = new VideoPolicy();

        static::assertFalse($policy->attachAnimeThemeEntry($viewer));
        static::assertTrue($policy->attachAnimeThemeEntry($editor));
        static::assertTrue($policy->attachAnimeThemeEntry($admin));
    }

    /**
     * A contributor or admin may detach an entry from a video.
     *
     * @return void
     */
    public function testDetachEntry(): void
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

        $policy = new VideoPolicy();

        static::assertFalse($policy->detachAnimeThemeEntry($viewer));
        static::assertTrue($policy->detachAnimeThemeEntry($editor));
        static::assertTrue($policy->detachAnimeThemeEntry($admin));
    }
}
