<?php

declare(strict_types=1);

namespace Tests\Unit\Policies\Wiki;

use App\Models\Auth\User;
use App\Policies\Wiki\SongPolicy;
use Illuminate\Foundation\Testing\WithoutEvents;
use Tests\TestCase;

/**
 * Class SongPolicyTest.
 */
class SongPolicyTest extends TestCase
{
    use WithoutEvents;

    /**
     * Any user regardless of role can view any song.
     *
     * @return void
     */
    public function testViewAny(): void
    {
        $policy = new SongPolicy();

        static::assertTrue($policy->viewAny());
    }

    /**
     * Any user regardless of role can view a song.
     *
     * @return void
     */
    public function testView(): void
    {
        $policy = new SongPolicy();

        static::assertTrue($policy->view());
    }

    /**
     * A contributor or admin may create a song.
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

        $policy = new SongPolicy();

        static::assertFalse($policy->create($viewer));
        static::assertTrue($policy->create($editor));
        static::assertTrue($policy->create($admin));
    }

    /**
     * A contributor or admin may update a song.
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

        $policy = new SongPolicy();

        static::assertFalse($policy->update($viewer));
        static::assertTrue($policy->update($editor));
        static::assertTrue($policy->update($admin));
    }

    /**
     * A contributor or admin may delete a song.
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

        $policy = new SongPolicy();

        static::assertFalse($policy->delete($viewer));
        static::assertTrue($policy->delete($editor));
        static::assertTrue($policy->delete($admin));
    }

    /**
     * A contributor or admin may restore a song.
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

        $policy = new SongPolicy();

        static::assertFalse($policy->restore($viewer));
        static::assertTrue($policy->restore($editor));
        static::assertTrue($policy->restore($admin));
    }

    /**
     * A contributor or admin may force delete a song.
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

        $policy = new SongPolicy();

        static::assertFalse($policy->forceDelete($viewer));
        static::assertFalse($policy->forceDelete($editor));
        static::assertTrue($policy->forceDelete($admin));
    }

    /**
     * An admin can add a theme to a song.
     *
     * @return void
     */
    public function testAddTheme(): void
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

        $policy = new SongPolicy();

        static::assertFalse($policy->addTheme($viewer));
        static::assertTrue($policy->addTheme($editor));
        static::assertTrue($policy->addTheme($admin));
    }

    /**
     * A contributor or admin may attach any artist to a song.
     *
     * @return void
     */
    public function testAttachAnyArtist(): void
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

        $policy = new SongPolicy();

        static::assertFalse($policy->attachAnyArtist($viewer));
        static::assertTrue($policy->attachAnyArtist($editor));
        static::assertTrue($policy->attachAnyArtist($admin));
    }

    /**
     * A contributor or admin may attach an artist to a song.
     *
     * @return void
     */
    public function testAttachArtist(): void
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

        $policy = new SongPolicy();

        static::assertFalse($policy->attachArtist($viewer));
        static::assertTrue($policy->attachArtist($editor));
        static::assertTrue($policy->attachArtist($admin));
    }

    /**
     * A contributor or admin may detach an artist from a song.
     *
     * @return void
     */
    public function testDetachArtist(): void
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

        $policy = new SongPolicy();

        static::assertFalse($policy->detachArtist($viewer));
        static::assertTrue($policy->detachArtist($editor));
        static::assertTrue($policy->detachArtist($admin));
    }
}
