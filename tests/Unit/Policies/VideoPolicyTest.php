<?php

namespace Tests\Unit\Policies;

use App\Models\Anime;
use App\Models\Entry;
use App\Models\Theme;
use App\Models\User;
use App\Models\Video;
use App\Policies\VideoPolicy;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class VideoPolicyTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Any user regardless of role can view any video.
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

        $policy = new VideoPolicy();

        $this->assertTrue($policy->viewAny($viewer));
        $this->assertTrue($policy->viewAny($editor));
        $this->assertTrue($policy->viewAny($admin));
    }

    /**
     * Any user regardless of role can view a video.
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

        $video = Video::factory()->create();
        $policy = new VideoPolicy();

        $this->assertTrue($policy->view($viewer, $video));
        $this->assertTrue($policy->view($editor, $video));
        $this->assertTrue($policy->view($admin, $video));
    }

    /**
     * A contributor or admin may create a video.
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

        $policy = new VideoPolicy();

        $this->assertFalse($policy->create($viewer));
        $this->assertFalse($policy->create($editor));
        $this->assertTrue($policy->create($admin));
    }

    /**
     * A contributor or admin may update a video.
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

        $video = Video::factory()->create();
        $policy = new VideoPolicy();

        $this->assertFalse($policy->update($viewer, $video));
        $this->assertTrue($policy->update($editor, $video));
        $this->assertTrue($policy->update($admin, $video));
    }

    /**
     * A contributor or admin may delete a video.
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

        $video = Video::factory()->create();
        $policy = new VideoPolicy();

        $this->assertFalse($policy->delete($viewer, $video));
        $this->assertFalse($policy->delete($editor, $video));
        $this->assertTrue($policy->delete($admin, $video));
    }

    /**
     * A contributor or admin may restore a video.
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

        $video = Video::factory()->create();
        $policy = new VideoPolicy();

        $this->assertFalse($policy->restore($viewer, $video));
        $this->assertFalse($policy->restore($editor, $video));
        $this->assertTrue($policy->restore($admin, $video));
    }

    /**
     * A contributor or admin may force delete a video.
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

        $video = Video::factory()->create();
        $policy = new VideoPolicy();

        $this->assertFalse($policy->forceDelete($viewer, $video));
        $this->assertFalse($policy->forceDelete($editor, $video));
        $this->assertTrue($policy->forceDelete($admin, $video));
    }

    /**
     * A contributor or admin may attach any entry to a video.
     *
     * @return void
     */
    public function testAttachAnyEntry()
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

        $video = Video::factory()->create();
        $policy = new VideoPolicy();

        $this->assertFalse($policy->attachAnyEntry($viewer, $video));
        $this->assertFalse($policy->attachAnyEntry($editor, $video));
        $this->assertTrue($policy->attachAnyEntry($admin, $video));
    }

    /**
     * A contributor or admin may attach an entry to a video if not already attached.
     *
     * @return void
     */
    public function testAttachEntry()
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

        $video = Video::factory()->create();
        $entry = Entry::factory()
            ->for(Theme::factory()->for(Anime::factory()))
            ->create();
        $policy = new VideoPolicy();

        $this->assertFalse($policy->attachEntry($viewer, $video, $entry));
        $this->assertFalse($policy->attachEntry($editor, $video, $entry));
        $this->assertTrue($policy->attachEntry($admin, $video, $entry));
    }

    /**
     * A contributor or admin may detach an entry from a video.
     *
     * @return void
     */
    public function testDetachEntry()
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

        $video = Video::factory()->create();
        $entry = Entry::factory()
            ->for(Theme::factory()->for(Anime::factory()))
            ->create();
        $policy = new VideoPolicy();

        $this->assertFalse($policy->detachEntry($viewer, $video, $entry));
        $this->assertFalse($policy->detachEntry($editor, $video, $entry));
        $this->assertTrue($policy->detachEntry($admin, $video, $entry));
    }
}
