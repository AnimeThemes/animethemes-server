<?php

namespace Tests\Unit\Policies;

use App\Models\Invitation;
use App\Models\User;
use App\Policies\InvitationPolicy;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class InvitationPolicyTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Any user regardless of role can view any invitation.
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

        $policy = new InvitationPolicy();

        $this->assertFalse($policy->viewAny($viewer));
        $this->assertFalse($policy->viewAny($editor));
        $this->assertTrue($policy->viewAny($admin));
    }

    /**
     * Any user regardless of role can view an invitation.
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

        $invitation = Invitation::factory()->create();
        $policy = new InvitationPolicy();

        $this->assertFalse($policy->view($viewer, $invitation));
        $this->assertFalse($policy->view($editor, $invitation));
        $this->assertTrue($policy->view($admin, $invitation));
    }

    /**
     * A contributor or admin may create an invitation.
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

        $policy = new InvitationPolicy();

        $this->assertFalse($policy->create($viewer));
        $this->assertFalse($policy->create($editor));
        $this->assertTrue($policy->create($admin));
    }

    /**
     * A contributor or admin may update an invitation.
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

        $invitation = Invitation::factory()->create();
        $policy = new InvitationPolicy();

        $this->assertFalse($policy->update($viewer, $invitation));
        $this->assertFalse($policy->update($editor, $invitation));
        $this->assertTrue($policy->update($admin, $invitation));
    }

    /**
     * A contributor or admin may delete an invitation.
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

        $invitation = Invitation::factory()->create();
        $policy = new InvitationPolicy();

        $this->assertFalse($policy->delete($viewer, $invitation));
        $this->assertFalse($policy->delete($editor, $invitation));
        $this->assertTrue($policy->delete($admin, $invitation));
    }

    /**
     * A contributor or admin may restore an invitation.
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

        $invitation = Invitation::factory()->create();
        $policy = new InvitationPolicy();

        $this->assertFalse($policy->restore($viewer, $invitation));
        $this->assertFalse($policy->restore($editor, $invitation));
        $this->assertTrue($policy->restore($admin, $invitation));
    }

    /**
     * A contributor or admin may force delete an invitation.
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

        $invitation = Invitation::factory()->create();
        $policy = new InvitationPolicy();

        $this->assertFalse($policy->forceDelete($viewer, $invitation));
        $this->assertFalse($policy->forceDelete($editor, $invitation));
        $this->assertTrue($policy->forceDelete($admin, $invitation));
    }
}
