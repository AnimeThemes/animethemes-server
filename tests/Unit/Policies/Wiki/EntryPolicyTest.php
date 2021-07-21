<?php

declare(strict_types=1);

namespace Tests\Unit\Policies\Wiki;

use App\Models\Auth\User;
use App\Models\Wiki\Anime;
use App\Models\Wiki\Entry;
use App\Models\Wiki\Theme;
use App\Models\Wiki\Video;
use App\Policies\Wiki\EntryPolicy;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Class EntryPolicyTest.
 */
class EntryPolicyTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Any user regardless of role can view any entry.
     *
     * @return void
     */
    public function testViewAny()
    {
        $policy = new EntryPolicy();

        static::assertTrue($policy->viewAny());
        static::assertTrue($policy->viewAny());
        static::assertTrue($policy->viewAny());
    }

    /**
     * Any user regardless of role can view an entry.
     *
     * @return void
     */
    public function testView()
    {
        $policy = new EntryPolicy();

        static::assertTrue($policy->view());
        static::assertTrue($policy->view());
        static::assertTrue($policy->view());
    }

    /**
     * A contributor or admin may create an entry.
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

        $policy = new EntryPolicy();

        static::assertFalse($policy->create($viewer));
        static::assertTrue($policy->create($editor));
        static::assertTrue($policy->create($admin));
    }

    /**
     * A contributor or admin may update an entry.
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

        $policy = new EntryPolicy();

        static::assertFalse($policy->update($viewer));
        static::assertTrue($policy->update($editor));
        static::assertTrue($policy->update($admin));
    }

    /**
     * A contributor or admin may delete an entry.
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

        $policy = new EntryPolicy();

        static::assertFalse($policy->delete($viewer));
        static::assertTrue($policy->delete($editor));
        static::assertTrue($policy->delete($admin));
    }

    /**
     * A contributor or admin may restore an entry.
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

        $policy = new EntryPolicy();

        static::assertFalse($policy->restore($viewer));
        static::assertTrue($policy->restore($editor));
        static::assertTrue($policy->restore($admin));
    }

    /**
     * A contributor or admin may force delete an entry.
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

        $policy = new EntryPolicy();

        static::assertFalse($policy->forceDelete($viewer));
        static::assertFalse($policy->forceDelete($editor));
        static::assertTrue($policy->forceDelete($admin));
    }

    /**
     * A contributor or admin may attach any video to an entry.
     *
     * @return void
     */
    public function testAttachAnyVideo()
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

        $policy = new EntryPolicy();

        static::assertFalse($policy->attachAnyVideo($viewer));
        static::assertTrue($policy->attachAnyVideo($editor));
        static::assertTrue($policy->attachAnyVideo($admin));
    }

    /**
     * A contributor or admin may attach a video to an entry if not already attached.
     *
     * @return void
     */
    public function testAttachNewVideo()
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

        $entry = Entry::factory()
            ->for(Theme::factory()->for(Anime::factory()))
            ->createOne();
        $video = Video::factory()->createOne();
        $policy = new EntryPolicy();

        static::assertFalse($policy->attachVideo($viewer, $entry, $video));
        static::assertTrue($policy->attachVideo($editor, $entry, $video));
        static::assertTrue($policy->attachVideo($admin, $entry, $video));
    }

    /**
     * If a video is already attached to an entry, no role may attach it.
     *
     * @return void
     */
    public function testAttachExistingVideo()
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

        $entry = Entry::factory()
            ->for(Theme::factory()->for(Anime::factory()))
            ->createOne();
        $video = Video::factory()->createOne();
        $entry->videos()->attach($video);
        $policy = new EntryPolicy();

        static::assertFalse($policy->attachVideo($viewer, $entry, $video));
        static::assertFalse($policy->attachVideo($editor, $entry, $video));
        static::assertFalse($policy->attachVideo($admin, $entry, $video));
    }

    /**
     * A contributor or admin may detach a video from an anime.
     *
     * @return void
     */
    public function testDetachVideo()
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

        $policy = new EntryPolicy();

        static::assertFalse($policy->detachVideo($viewer));
        static::assertTrue($policy->detachVideo($editor));
        static::assertTrue($policy->detachVideo($admin));
    }
}
