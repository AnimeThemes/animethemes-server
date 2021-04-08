<?php

namespace Tests\Unit\Policies;

use App\Models\Announcement;
use App\Models\User;
use App\Policies\AnnouncementPolicy;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithoutEvents;
use Tests\TestCase;

class AnnouncementPolicyTest extends TestCase
{
    use RefreshDatabase, WithoutEvents;

    /**
     * Any user regardless of role can view any announcement.
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

        $policy = new AnnouncementPolicy();

        $this->assertFalse($policy->viewAny($viewer));
        $this->assertFalse($policy->viewAny($editor));
        $this->assertTrue($policy->viewAny($admin));
    }

    /**
     * Any user regardless of role can view an announcement.
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

        $announcement = Announcement::factory()->create();
        $policy = new AnnouncementPolicy();

        $this->assertFalse($policy->view($viewer, $announcement));
        $this->assertFalse($policy->view($editor, $announcement));
        $this->assertTrue($policy->view($admin, $announcement));
    }

    /**
     * A contributor or admin may create an announcement.
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

        $policy = new AnnouncementPolicy();

        $this->assertFalse($policy->create($viewer));
        $this->assertFalse($policy->create($editor));
        $this->assertTrue($policy->create($admin));
    }

    /**
     * A contributor or admin may update an announcement.
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

        $announcement = Announcement::factory()->create();
        $policy = new AnnouncementPolicy();

        $this->assertFalse($policy->update($viewer, $announcement));
        $this->assertFalse($policy->update($editor, $announcement));
        $this->assertTrue($policy->update($admin, $announcement));
    }

    /**
     * A contributor or admin may delete an announcement.
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

        $announcement = Announcement::factory()->create();
        $policy = new AnnouncementPolicy();

        $this->assertFalse($policy->delete($viewer, $announcement));
        $this->assertFalse($policy->delete($editor, $announcement));
        $this->assertTrue($policy->delete($admin, $announcement));
    }

    /**
     * A contributor or admin may restore an announcement.
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

        $announcement = Announcement::factory()->create();
        $policy = new AnnouncementPolicy();

        $this->assertFalse($policy->restore($viewer, $announcement));
        $this->assertFalse($policy->restore($editor, $announcement));
        $this->assertTrue($policy->restore($admin, $announcement));
    }

    /**
     * A contributor or admin may force delete an announcement.
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

        $announcement = Announcement::factory()->create();
        $policy = new AnnouncementPolicy();

        $this->assertFalse($policy->forceDelete($viewer, $announcement));
        $this->assertFalse($policy->forceDelete($editor, $announcement));
        $this->assertTrue($policy->forceDelete($admin, $announcement));
    }
}
