<?php

namespace Tests\Unit\Policies\Billing;

use App\Models\Billing\Transaction;
use App\Models\User;
use App\Policies\Billing\TransactionPolicy;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TransactionPolicyTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Any user regardless of role can view any transaction.
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

        $policy = new TransactionPolicy();

        $this->assertFalse($policy->viewAny($viewer));
        $this->assertFalse($policy->viewAny($editor));
        $this->assertTrue($policy->viewAny($admin));
    }

    /**
     * Any user regardless of role can view a transaction.
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

        $transaction = Transaction::factory()->create();
        $policy = new TransactionPolicy();

        $this->assertFalse($policy->view($viewer, $transaction));
        $this->assertFalse($policy->view($editor, $transaction));
        $this->assertTrue($policy->view($admin, $transaction));
    }

    /**
     * A contributor or admin may create a transaction.
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

        $policy = new TransactionPolicy();

        $this->assertFalse($policy->create($viewer));
        $this->assertFalse($policy->create($editor));
        $this->assertTrue($policy->create($admin));
    }

    /**
     * A contributor or admin may update a transaction.
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

        $transaction = Transaction::factory()->create();
        $policy = new TransactionPolicy();

        $this->assertFalse($policy->update($viewer, $transaction));
        $this->assertFalse($policy->update($editor, $transaction));
        $this->assertTrue($policy->update($admin, $transaction));
    }

    /**
     * A contributor or admin may delete a transaction.
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

        $transaction = Transaction::factory()->create();
        $policy = new TransactionPolicy();

        $this->assertFalse($policy->delete($viewer, $transaction));
        $this->assertFalse($policy->delete($editor, $transaction));
        $this->assertTrue($policy->delete($admin, $transaction));
    }

    /**
     * A contributor or admin may restore a transaction.
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

        $transaction = Transaction::factory()->create();
        $policy = new TransactionPolicy();

        $this->assertFalse($policy->restore($viewer, $transaction));
        $this->assertFalse($policy->restore($editor, $transaction));
        $this->assertTrue($policy->restore($admin, $transaction));
    }

    /**
     * A contributor or admin may force delete a transaction.
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

        $transaction = Transaction::factory()->create();
        $policy = new TransactionPolicy();

        $this->assertFalse($policy->forceDelete($viewer, $transaction));
        $this->assertFalse($policy->forceDelete($editor, $transaction));
        $this->assertTrue($policy->forceDelete($admin, $transaction));
    }
}
