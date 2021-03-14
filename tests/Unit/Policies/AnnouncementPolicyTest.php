<?php

namespace Tests\Unit\Policies;

use App\Enums\UserRole;
use App\Models\Announcement;
use App\Models\User;
use App\Policies\AnnouncementPolicy;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AnnouncementPolicyTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Any user regardless of role can view any announcement.
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

        $policy = new AnnouncementPolicy();

        $this->assertFalse($policy->viewAny($read_only));
        $this->assertFalse($policy->viewAny($contributor));
        $this->assertTrue($policy->viewAny($admin));
    }

    /**
     * Any user regardless of role can view an announcement.
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

        $announcement = Announcement::factory()->create();
        $policy = new AnnouncementPolicy();

        $this->assertFalse($policy->view($read_only, $announcement));
        $this->assertFalse($policy->view($contributor, $announcement));
        $this->assertTrue($policy->view($admin, $announcement));
    }

    /**
     * A contributor or admin may create an announcement.
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

        $policy = new AnnouncementPolicy();

        $this->assertFalse($policy->create($read_only));
        $this->assertFalse($policy->create($contributor));
        $this->assertTrue($policy->create($admin));
    }

    /**
     * A contributor or admin may update an announcement.
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

        $announcement = Announcement::factory()->create();
        $policy = new AnnouncementPolicy();

        $this->assertFalse($policy->update($read_only, $announcement));
        $this->assertFalse($policy->update($contributor, $announcement));
        $this->assertTrue($policy->update($admin, $announcement));
    }

    /**
     * A contributor or admin may delete an announcement.
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

        $announcement = Announcement::factory()->create();
        $policy = new AnnouncementPolicy();

        $this->assertFalse($policy->delete($read_only, $announcement));
        $this->assertFalse($policy->delete($contributor, $announcement));
        $this->assertTrue($policy->delete($admin, $announcement));
    }

    /**
     * A contributor or admin may restore an announcement.
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

        $announcement = Announcement::factory()->create();
        $policy = new AnnouncementPolicy();

        $this->assertFalse($policy->restore($read_only, $announcement));
        $this->assertFalse($policy->restore($contributor, $announcement));
        $this->assertTrue($policy->restore($admin, $announcement));
    }

    /**
     * A contributor or admin may force delete an announcement.
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

        $announcement = Announcement::factory()->create();
        $policy = new AnnouncementPolicy();

        $this->assertFalse($policy->forceDelete($read_only, $announcement));
        $this->assertFalse($policy->forceDelete($contributor, $announcement));
        $this->assertTrue($policy->forceDelete($admin, $announcement));
    }
}
