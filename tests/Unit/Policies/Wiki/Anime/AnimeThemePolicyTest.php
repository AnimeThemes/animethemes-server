<?php

declare(strict_types=1);

namespace Tests\Unit\Policies\Wiki\Anime;

use App\Models\Auth\User;
use App\Policies\Wiki\Anime\AnimeThemePolicy;
use Tests\TestCase;

/**
 * Class ThemePolicyTest.
 */
class AnimeThemePolicyTest extends TestCase
{
    /**
     * Any user regardless of role can view any theme.
     *
     * @return void
     */
    public function testViewAny(): void
    {
        $policy = new AnimeThemePolicy();

        static::assertTrue($policy->viewAny());
    }

    /**
     * Any user regardless of role can view a theme.
     *
     * @return void
     */
    public function testView(): void
    {
        $policy = new AnimeThemePolicy();

        static::assertTrue($policy->view());
    }

    /**
     * A contributor or admin may create a theme.
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

        $policy = new AnimeThemePolicy();

        static::assertFalse($policy->create($viewer));
        static::assertTrue($policy->create($editor));
        static::assertTrue($policy->create($admin));
    }

    /**
     * A contributor or admin may update a theme.
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

        $policy = new AnimeThemePolicy();

        static::assertFalse($policy->update($viewer));
        static::assertTrue($policy->update($editor));
        static::assertTrue($policy->update($admin));
    }

    /**
     * A contributor or admin may delete a theme.
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

        $policy = new AnimeThemePolicy();

        static::assertFalse($policy->delete($viewer));
        static::assertTrue($policy->delete($editor));
        static::assertTrue($policy->delete($admin));
    }

    /**
     * A contributor or admin may restore a theme.
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

        $policy = new AnimeThemePolicy();

        static::assertFalse($policy->restore($viewer));
        static::assertTrue($policy->restore($editor));
        static::assertTrue($policy->restore($admin));
    }

    /**
     * A contributor or admin may force delete a theme.
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

        $policy = new AnimeThemePolicy();

        static::assertFalse($policy->forceDelete($viewer));
        static::assertFalse($policy->forceDelete($editor));
        static::assertTrue($policy->forceDelete($admin));
    }
}
