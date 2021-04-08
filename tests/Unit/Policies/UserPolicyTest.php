<?php

namespace Tests\Unit\Policies;

use App\Models\User;
use App\Policies\UserPolicy;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithoutEvents;
use Tests\TestCase;

class UserPolicyTest extends TestCase
{
    use RefreshDatabase, WithoutEvents;

    /**
     * Any user regardless of role can view any user.
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

        $policy = new UserPolicy();

        $this->assertFalse($policy->viewAny($viewer));
        $this->assertFalse($policy->viewAny($editor));
        $this->assertTrue($policy->viewAny($admin));
    }

    /**
     * Any user regardless of role can view a user.
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

        $user = User::factory()->create();
        $policy = new UserPolicy();

        $this->assertFalse($policy->view($viewer, $user));
        $this->assertFalse($policy->view($editor, $user));
        $this->assertTrue($policy->view($admin, $user));
        $this->assertTrue($policy->view($user, $user));
    }

    /**
     * A contributor or admin may create a user.
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

        $policy = new UserPolicy();

        $this->assertFalse($policy->create($viewer));
        $this->assertFalse($policy->create($editor));
        $this->assertTrue($policy->create($admin));
    }

    /**
     * A contributor or admin may update a user.
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

        $user = User::factory()->create();
        $policy = new UserPolicy();

        $this->assertFalse($policy->update($viewer, $user));
        $this->assertFalse($policy->update($editor, $user));
        $this->assertTrue($policy->update($admin, $user));
    }

    /**
     * A contributor or admin may delete a user.
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

        $user = User::factory()->create();
        $policy = new UserPolicy();

        $this->assertFalse($policy->delete($viewer, $user));
        $this->assertFalse($policy->delete($editor, $user));
        $this->assertTrue($policy->delete($admin, $user));
    }

    /**
     * A contributor or admin may restore a user.
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

        $user = User::factory()->create();
        $policy = new UserPolicy();

        $this->assertFalse($policy->restore($viewer, $user));
        $this->assertFalse($policy->restore($editor, $user));
        $this->assertTrue($policy->restore($admin, $user));
    }

    /**
     * A contributor or admin may force delete a user.
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

        $user = User::factory()->create();
        $policy = new UserPolicy();

        $this->assertFalse($policy->forceDelete($viewer, $user));
        $this->assertFalse($policy->forceDelete($editor, $user));
        $this->assertTrue($policy->forceDelete($admin, $user));
    }
}
