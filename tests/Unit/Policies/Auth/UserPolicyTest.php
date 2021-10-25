<?php

declare(strict_types=1);

namespace Tests\Unit\Policies\Auth;

use App\Models\Auth\User;
use App\Policies\Auth\UserPolicy;
use Illuminate\Foundation\Testing\WithoutEvents;
use Tests\TestCase;

/**
 * Class UserPolicyTest.
 */
class UserPolicyTest extends TestCase
{
    use WithoutEvents;

    /**
     * An admin can view any user.
     *
     * @return void
     */
    public function testViewAny()
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

        $policy = new UserPolicy();

        static::assertFalse($policy->viewAny($viewer));
        static::assertFalse($policy->viewAny($editor));
        static::assertTrue($policy->viewAny($admin));
    }

    /**
     * An admin can view a user.
     *
     * @return void
     */
    public function testView()
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

        $user = User::factory()->createOne();
        $policy = new UserPolicy();

        static::assertFalse($policy->view($viewer, $user));
        static::assertFalse($policy->view($editor, $user));
        static::assertTrue($policy->view($admin, $user));
        static::assertTrue($policy->view($user, $user));
    }

    /**
     * An admin may create a user.
     *
     * @return void
     */
    public function testCreate()
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

        $policy = new UserPolicy();

        static::assertFalse($policy->create($viewer));
        static::assertFalse($policy->create($editor));
        static::assertTrue($policy->create($admin));
    }

    /**
     * An admin may update a user.
     *
     * @return void
     */
    public function testUpdate()
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

        $user = User::factory()->createOne();
        $policy = new UserPolicy();

        static::assertFalse($policy->update($viewer, $user));
        static::assertFalse($policy->update($editor, $user));
        static::assertTrue($policy->update($admin, $user));
    }

    /**
     * An admin may delete a user.
     *
     * @return void
     */
    public function testDelete()
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

        $policy = new UserPolicy();

        static::assertFalse($policy->delete($viewer));
        static::assertFalse($policy->delete($editor));
        static::assertTrue($policy->delete($admin));
    }

    /**
     * An admin may restore a user.
     *
     * @return void
     */
    public function testRestore()
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

        $policy = new UserPolicy();

        static::assertFalse($policy->restore($viewer));
        static::assertFalse($policy->restore($editor));
        static::assertTrue($policy->restore($admin));
    }

    /**
     * An admin may force delete a user.
     *
     * @return void
     */
    public function testForceDelete()
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

        $policy = new UserPolicy();

        static::assertFalse($policy->forceDelete($viewer));
        static::assertFalse($policy->forceDelete($editor));
        static::assertTrue($policy->forceDelete($admin));
    }
}
