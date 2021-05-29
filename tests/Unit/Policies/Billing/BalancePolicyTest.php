<?php

namespace Tests\Unit\Policies\Billing;

use App\Models\Billing\Balance;
use App\Models\User;
use App\Policies\Billing\BalancePolicy;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BalancePolicyTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Any user regardless of role can view any balance.
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

        $policy = new BalancePolicy();

        $this->assertFalse($policy->viewAny($viewer));
        $this->assertFalse($policy->viewAny($editor));
        $this->assertTrue($policy->viewAny($admin));
    }

    /**
     * Any user regardless of role can view a balance.
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

        $balance = Balance::factory()->create();
        $policy = new BalancePolicy();

        $this->assertFalse($policy->view($viewer, $balance));
        $this->assertFalse($policy->view($editor, $balance));
        $this->assertTrue($policy->view($admin, $balance));
    }

    /**
     * A contributor or admin may create a balance.
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

        $policy = new BalancePolicy();

        $this->assertFalse($policy->create($viewer));
        $this->assertFalse($policy->create($editor));
        $this->assertTrue($policy->create($admin));
    }

    /**
     * A contributor or admin may update a balance.
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

        $balance = Balance::factory()->create();
        $policy = new BalancePolicy();

        $this->assertFalse($policy->update($viewer, $balance));
        $this->assertFalse($policy->update($editor, $balance));
        $this->assertTrue($policy->update($admin, $balance));
    }

    /**
     * A contributor or admin may delete a balance.
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

        $balance = Balance::factory()->create();
        $policy = new BalancePolicy();

        $this->assertFalse($policy->delete($viewer, $balance));
        $this->assertFalse($policy->delete($editor, $balance));
        $this->assertTrue($policy->delete($admin, $balance));
    }

    /**
     * A contributor or admin may restore a balance.
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

        $balance = Balance::factory()->create();
        $policy = new BalancePolicy();

        $this->assertFalse($policy->restore($viewer, $balance));
        $this->assertFalse($policy->restore($editor, $balance));
        $this->assertTrue($policy->restore($admin, $balance));
    }

    /**
     * A contributor or admin may force delete a balance.
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

        $balance = Balance::factory()->create();
        $policy = new BalancePolicy();

        $this->assertFalse($policy->forceDelete($viewer, $balance));
        $this->assertFalse($policy->forceDelete($editor, $balance));
        $this->assertTrue($policy->forceDelete($admin, $balance));
    }
}
