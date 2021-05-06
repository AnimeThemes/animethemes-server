<?php

namespace Tests\Unit\Policies;

use App\Models\Invoice;
use App\Models\User;
use App\Policies\InvoicePolicy;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithoutEvents;
use Tests\TestCase;

class InvoicePolicyTest extends TestCase
{
    use RefreshDatabase, WithoutEvents;

    /**
     * Any user regardless of role can view any invoice.
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

        $policy = new InvoicePolicy();

        $this->assertFalse($policy->viewAny($viewer));
        $this->assertFalse($policy->viewAny($editor));
        $this->assertTrue($policy->viewAny($admin));
    }

    /**
     * Any user regardless of role can view an invoice.
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

        $invoice = Invoice::factory()->create();
        $policy = new InvoicePolicy();

        $this->assertFalse($policy->view($viewer, $invoice));
        $this->assertFalse($policy->view($editor, $invoice));
        $this->assertTrue($policy->view($admin, $invoice));
    }

    /**
     * A contributor or admin may create an invoice.
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

        $policy = new InvoicePolicy();

        $this->assertFalse($policy->create($viewer));
        $this->assertFalse($policy->create($editor));
        $this->assertTrue($policy->create($admin));
    }

    /**
     * A contributor or admin may update an invoice.
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

        $invoice = Invoice::factory()->create();
        $policy = new InvoicePolicy();

        $this->assertFalse($policy->update($viewer, $invoice));
        $this->assertFalse($policy->update($editor, $invoice));
        $this->assertTrue($policy->update($admin, $invoice));
    }

    /**
     * A contributor or admin may delete an invoice.
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

        $invoice = Invoice::factory()->create();
        $policy = new InvoicePolicy();

        $this->assertFalse($policy->delete($viewer, $invoice));
        $this->assertFalse($policy->delete($editor, $invoice));
        $this->assertTrue($policy->delete($admin, $invoice));
    }

    /**
     * A contributor or admin may restore an invoice.
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

        $invoice = Invoice::factory()->create();
        $policy = new InvoicePolicy();

        $this->assertFalse($policy->restore($viewer, $invoice));
        $this->assertFalse($policy->restore($editor, $invoice));
        $this->assertTrue($policy->restore($admin, $invoice));
    }

    /**
     * A contributor or admin may force delete an invoice.
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

        $invoice = Invoice::factory()->create();
        $policy = new InvoicePolicy();

        $this->assertFalse($policy->forceDelete($viewer, $invoice));
        $this->assertFalse($policy->forceDelete($editor, $invoice));
        $this->assertTrue($policy->forceDelete($admin, $invoice));
    }
}
