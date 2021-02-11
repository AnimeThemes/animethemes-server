<?php

namespace Tests\Unit\Policies;

use App\Enums\UserRole;
use App\Models\Invitation;
use App\Models\User;
use App\Policies\InvitationPolicy;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class InvitationPolicyTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    /**
     * Any user regardless of role can view any invitation.
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

        $policy = new InvitationPolicy();

        $this->assertFalse($policy->viewAny($read_only));
        $this->assertFalse($policy->viewAny($contributor));
        $this->assertTrue($policy->viewAny($admin));
    }

    /**
     * Any user regardless of role can view an invitation.
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

        $invitation = Invitation::factory()->create();
        $policy = new InvitationPolicy();

        $this->assertFalse($policy->view($read_only, $invitation));
        $this->assertFalse($policy->view($contributor, $invitation));
        $this->assertTrue($policy->view($admin, $invitation));
    }

    /**
     * A contributor or admin may create an invitation.
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

        $policy = new InvitationPolicy();

        $this->assertFalse($policy->create($read_only));
        $this->assertFalse($policy->create($contributor));
        $this->assertTrue($policy->create($admin));
    }

    /**
     * A contributor or admin may update an invitation.
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

        $invitation = Invitation::factory()->create();
        $policy = new InvitationPolicy();

        $this->assertFalse($policy->update($read_only, $invitation));
        $this->assertFalse($policy->update($contributor, $invitation));
        $this->assertTrue($policy->update($admin, $invitation));
    }

    /**
     * A contributor or admin may delete an invitation.
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

        $invitation = Invitation::factory()->create();
        $policy = new InvitationPolicy();

        $this->assertFalse($policy->delete($read_only, $invitation));
        $this->assertFalse($policy->delete($contributor, $invitation));
        $this->assertTrue($policy->delete($admin, $invitation));
    }

    /**
     * A contributor or admin may restore an invitation.
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

        $invitation = Invitation::factory()->create();
        $policy = new InvitationPolicy();

        $this->assertFalse($policy->restore($read_only, $invitation));
        $this->assertFalse($policy->restore($contributor, $invitation));
        $this->assertTrue($policy->restore($admin, $invitation));
    }

    /**
     * A contributor or admin may force delete an invitation.
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

        $invitation = Invitation::factory()->create();
        $policy = new InvitationPolicy();

        $this->assertFalse($policy->forceDelete($read_only, $invitation));
        $this->assertFalse($policy->forceDelete($contributor, $invitation));
        $this->assertTrue($policy->forceDelete($admin, $invitation));
    }
}
