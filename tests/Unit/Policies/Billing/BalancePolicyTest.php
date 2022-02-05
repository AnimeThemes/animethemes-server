<?php

declare(strict_types=1);

namespace Tests\Unit\Policies\Billing;

use App\Models\Auth\User;
use App\Policies\Billing\BalancePolicy;
use Tests\TestCase;

/**
 * Class BalancePolicyTest.
 */
class BalancePolicyTest extends TestCase
{
    /**
     * An admin can view any balance.
     *
     * @return void
     */
    public function testViewAny(): void
    {
        $viewer = User::factory()
            ->withCurrentTeam('viewer')
            ->createOne();

        $editor = User::factory()
            ->withCurrentTeam('editor')
            ->createOne();

        $admin = User::factory()
            ->withCurrentTeam('admin')
            ->createOne();

        $policy = new BalancePolicy();

        static::assertFalse($policy->viewAny($viewer));
        static::assertFalse($policy->viewAny($editor));
        static::assertTrue($policy->viewAny($admin));
    }

    /**
     * An admin can view a balance.
     *
     * @return void
     */
    public function testView(): void
    {
        $viewer = User::factory()
            ->withCurrentTeam('viewer')
            ->createOne();

        $editor = User::factory()
            ->withCurrentTeam('editor')
            ->createOne();

        $admin = User::factory()
            ->withCurrentTeam('admin')
            ->createOne();

        $policy = new BalancePolicy();

        static::assertFalse($policy->view($viewer));
        static::assertFalse($policy->view($editor));
        static::assertTrue($policy->view($admin));
    }

    /**
     * An admin may create a balance.
     *
     * @return void
     */
    public function testCreate(): void
    {
        $viewer = User::factory()
            ->withCurrentTeam('viewer')
            ->createOne();

        $editor = User::factory()
            ->withCurrentTeam('editor')
            ->createOne();

        $admin = User::factory()
            ->withCurrentTeam('admin')
            ->createOne();

        $policy = new BalancePolicy();

        static::assertFalse($policy->create($viewer));
        static::assertFalse($policy->create($editor));
        static::assertTrue($policy->create($admin));
    }

    /**
     * An admin may update a balance.
     *
     * @return void
     */
    public function testUpdate(): void
    {
        $viewer = User::factory()
            ->withCurrentTeam('viewer')
            ->createOne();

        $editor = User::factory()
            ->withCurrentTeam('editor')
            ->createOne();

        $admin = User::factory()
            ->withCurrentTeam('admin')
            ->createOne();

        $policy = new BalancePolicy();

        static::assertFalse($policy->update($viewer));
        static::assertFalse($policy->update($editor));
        static::assertTrue($policy->update($admin));
    }

    /**
     * An admin may delete a balance.
     *
     * @return void
     */
    public function testDelete(): void
    {
        $viewer = User::factory()
            ->withCurrentTeam('viewer')
            ->createOne();

        $editor = User::factory()
            ->withCurrentTeam('editor')
            ->createOne();

        $admin = User::factory()
            ->withCurrentTeam('admin')
            ->createOne();

        $policy = new BalancePolicy();

        static::assertFalse($policy->delete($viewer));
        static::assertFalse($policy->delete($editor));
        static::assertTrue($policy->delete($admin));
    }

    /**
     * An admin may restore a balance.
     *
     * @return void
     */
    public function testRestore(): void
    {
        $viewer = User::factory()
            ->withCurrentTeam('viewer')
            ->createOne();

        $editor = User::factory()
            ->withCurrentTeam('editor')
            ->createOne();

        $admin = User::factory()
            ->withCurrentTeam('admin')
            ->createOne();

        $policy = new BalancePolicy();

        static::assertFalse($policy->restore($viewer));
        static::assertFalse($policy->restore($editor));
        static::assertTrue($policy->restore($admin));
    }

    /**
     * An admin may force delete a balance.
     *
     * @return void
     */
    public function testForceDelete(): void
    {
        $viewer = User::factory()
            ->withCurrentTeam('viewer')
            ->createOne();

        $editor = User::factory()
            ->withCurrentTeam('editor')
            ->createOne();

        $admin = User::factory()
            ->withCurrentTeam('admin')
            ->createOne();

        $policy = new BalancePolicy();

        static::assertFalse($policy->forceDelete($viewer));
        static::assertFalse($policy->forceDelete($editor));
        static::assertTrue($policy->forceDelete($admin));
    }
}
