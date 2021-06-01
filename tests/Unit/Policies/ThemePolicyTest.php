<?php declare(strict_types=1);

namespace Policies;

use App\Models\User;
use App\Policies\ThemePolicy;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Class ThemePolicyTest
 * @package Policies
 */
class ThemePolicyTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Any user regardless of role can view any theme.
     *
     * @return void
     */
    public function testViewAny()
    {
        $policy = new ThemePolicy();

        static::assertTrue($policy->viewAny());
        static::assertTrue($policy->viewAny());
        static::assertTrue($policy->viewAny());
    }

    /**
     * Any user regardless of role can view a theme.
     *
     * @return void
     */
    public function testView()
    {
        $policy = new ThemePolicy();

        static::assertTrue($policy->view());
        static::assertTrue($policy->view());
        static::assertTrue($policy->view());
    }

    /**
     * A contributor or admin may create a theme.
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

        $policy = new ThemePolicy();

        static::assertFalse($policy->create($viewer));
        static::assertTrue($policy->create($editor));
        static::assertTrue($policy->create($admin));
    }

    /**
     * A contributor or admin may update a theme.
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

        $policy = new ThemePolicy();

        static::assertFalse($policy->update($viewer));
        static::assertTrue($policy->update($editor));
        static::assertTrue($policy->update($admin));
    }

    /**
     * A contributor or admin may delete a theme.
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

        $policy = new ThemePolicy();

        static::assertFalse($policy->delete($viewer));
        static::assertTrue($policy->delete($editor));
        static::assertTrue($policy->delete($admin));
    }

    /**
     * A contributor or admin may restore a theme.
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

        $policy = new ThemePolicy();

        static::assertFalse($policy->restore($viewer));
        static::assertTrue($policy->restore($editor));
        static::assertTrue($policy->restore($admin));
    }

    /**
     * A contributor or admin may force delete a theme.
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

        $policy = new ThemePolicy();

        static::assertFalse($policy->forceDelete($viewer));
        static::assertFalse($policy->forceDelete($editor));
        static::assertTrue($policy->forceDelete($admin));
    }
}
