<?php

declare(strict_types=1);

namespace Tests\Unit\Policies\Document;

use App\Models\Auth\User;
use App\Policies\Document\PagePolicy;
use Illuminate\Foundation\Testing\WithoutEvents;
use Tests\TestCase;

/**
 * Class PagePolicyTest.
 */
class PagePolicyTest extends TestCase
{
    use WithoutEvents;

    /**
     * An admin can view any page.
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

        $policy = new PagePolicy();

        static::assertTrue($policy->viewAny($viewer));
        static::assertTrue($policy->viewAny($editor));
        static::assertTrue($policy->viewAny($admin));
    }

    /**
     * An admin can view a page.
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

        $policy = new PagePolicy();

        static::assertTrue($policy->view($viewer));
        static::assertTrue($policy->view($editor));
        static::assertTrue($policy->view($admin));
    }

    /**
     * An admin may create a page.
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

        $policy = new PagePolicy();

        static::assertFalse($policy->create($viewer));
        static::assertTrue($policy->create($editor));
        static::assertTrue($policy->create($admin));
    }

    /**
     * An admin may update a page.
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

        $policy = new PagePolicy();

        static::assertFalse($policy->update($viewer));
        static::assertTrue($policy->update($editor));
        static::assertTrue($policy->update($admin));
    }

    /**
     * An admin may delete a page.
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

        $policy = new PagePolicy();

        static::assertFalse($policy->delete($viewer));
        static::assertTrue($policy->delete($editor));
        static::assertTrue($policy->delete($admin));
    }

    /**
     * An admin may restore a page.
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

        $policy = new PagePolicy();

        static::assertFalse($policy->restore($viewer));
        static::assertTrue($policy->restore($editor));
        static::assertTrue($policy->restore($admin));
    }

    /**
     * An admin may force delete a page.
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

        $policy = new PagePolicy();

        static::assertFalse($policy->forceDelete($viewer));
        static::assertFalse($policy->forceDelete($editor));
        static::assertTrue($policy->forceDelete($admin));
    }
}
