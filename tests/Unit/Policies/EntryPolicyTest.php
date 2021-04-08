<?php

namespace Tests\Unit\Policies;

use App\Models\Anime;
use App\Models\Entry;
use App\Models\Theme;
use App\Models\User;
use App\Models\Video;
use App\Policies\EntryPolicy;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

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
        $viewer = User::factory()
            ->withCurrentTeam('viewer')
            ->create();

        $editor = User::factory()
            ->withCurrentTeam('editor')
            ->create();

        $admin = User::factory()
            ->withCurrentTeam('admin')
            ->create();

        $policy = new EntryPolicy();

        $this->assertTrue($policy->viewAny($viewer));
        $this->assertTrue($policy->viewAny($editor));
        $this->assertTrue($policy->viewAny($admin));
    }

    /**
     * Any user regardless of role can view an entry.
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

        $entry = Entry::factory()
            ->for(Theme::factory()->for(Anime::factory()))
            ->create();
        $policy = new EntryPolicy();

        $this->assertTrue($policy->view($viewer, $entry));
        $this->assertTrue($policy->view($editor, $entry));
        $this->assertTrue($policy->view($admin, $entry));
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
            ->create();

        $editor = User::factory()
            ->withCurrentTeam('editor')
            ->create();

        $admin = User::factory()
            ->withCurrentTeam('admin')
            ->create();

        $policy = new EntryPolicy();

        $this->assertFalse($policy->create($viewer));
        $this->assertTrue($policy->create($editor));
        $this->assertTrue($policy->create($admin));
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
            ->create();

        $editor = User::factory()
            ->withCurrentTeam('editor')
            ->create();

        $admin = User::factory()
            ->withCurrentTeam('admin')
            ->create();

        $entry = Entry::factory()
            ->for(Theme::factory()->for(Anime::factory()))
            ->create();
        $policy = new EntryPolicy();

        $this->assertFalse($policy->update($viewer, $entry));
        $this->assertTrue($policy->update($editor, $entry));
        $this->assertTrue($policy->update($admin, $entry));
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
            ->create();

        $editor = User::factory()
            ->withCurrentTeam('editor')
            ->create();

        $admin = User::factory()
            ->withCurrentTeam('admin')
            ->create();

        $entry = Entry::factory()
            ->for(Theme::factory()->for(Anime::factory()))
            ->create();
        $policy = new EntryPolicy();

        $this->assertFalse($policy->delete($viewer, $entry));
        $this->assertTrue($policy->delete($editor, $entry));
        $this->assertTrue($policy->delete($admin, $entry));
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
            ->create();

        $editor = User::factory()
            ->withCurrentTeam('editor')
            ->create();

        $admin = User::factory()
            ->withCurrentTeam('admin')
            ->create();

        $entry = Entry::factory()
            ->for(Theme::factory()->for(Anime::factory()))
            ->create();
        $policy = new EntryPolicy();

        $this->assertFalse($policy->restore($viewer, $entry));
        $this->assertTrue($policy->restore($editor, $entry));
        $this->assertTrue($policy->restore($admin, $entry));
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
            ->create();

        $editor = User::factory()
            ->withCurrentTeam('editor')
            ->create();

        $admin = User::factory()
            ->withCurrentTeam('admin')
            ->create();

        $entry = Entry::factory()
            ->for(Theme::factory()->for(Anime::factory()))
            ->create();
        $policy = new EntryPolicy();

        $this->assertFalse($policy->forceDelete($viewer, $entry));
        $this->assertFalse($policy->forceDelete($editor, $entry));
        $this->assertTrue($policy->forceDelete($admin, $entry));
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
            ->create();

        $editor = User::factory()
            ->withCurrentTeam('editor')
            ->create();

        $admin = User::factory()
            ->withCurrentTeam('admin')
            ->create();

        $entry = Entry::factory()
            ->for(Theme::factory()->for(Anime::factory()))
            ->create();
        $policy = new EntryPolicy();

        $this->assertFalse($policy->attachAnyVideo($viewer, $entry));
        $this->assertTrue($policy->attachAnyVideo($editor, $entry));
        $this->assertTrue($policy->attachAnyVideo($admin, $entry));
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
            ->create();

        $editor = User::factory()
            ->withCurrentTeam('editor')
            ->create();

        $admin = User::factory()
            ->withCurrentTeam('admin')
            ->create();

        $entry = Entry::factory()
            ->for(Theme::factory()->for(Anime::factory()))
            ->create();
        $video = Video::factory()->create();
        $policy = new EntryPolicy();

        $this->assertFalse($policy->attachVideo($viewer, $entry, $video));
        $this->assertTrue($policy->attachVideo($editor, $entry, $video));
        $this->assertTrue($policy->attachVideo($admin, $entry, $video));
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
            ->create();

        $editor = User::factory()
            ->withCurrentTeam('editor')
            ->create();

        $admin = User::factory()
            ->withCurrentTeam('admin')
            ->create();

        $entry = Entry::factory()
            ->for(Theme::factory()->for(Anime::factory()))
            ->create();
        $video = Video::factory()->create();
        $entry->videos()->attach($video);
        $policy = new EntryPolicy();

        $this->assertFalse($policy->attachVideo($viewer, $entry, $video));
        $this->assertFalse($policy->attachVideo($editor, $entry, $video));
        $this->assertFalse($policy->attachVideo($admin, $entry, $video));
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
            ->create();

        $editor = User::factory()
            ->withCurrentTeam('editor')
            ->create();

        $admin = User::factory()
            ->withCurrentTeam('admin')
            ->create();

        $entry = Entry::factory()
            ->for(Theme::factory()->for(Anime::factory()))
            ->create();
        $video = Video::factory()->create();
        $policy = new EntryPolicy();

        $this->assertFalse($policy->detachVideo($viewer, $entry, $video));
        $this->assertTrue($policy->detachVideo($editor, $entry, $video));
        $this->assertTrue($policy->detachVideo($admin, $entry, $video));
    }
}
