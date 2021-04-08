<?php

namespace Tests\Unit\Policies;

use App\Models\Anime;
use App\Models\Theme;
use App\Models\User;
use App\Policies\ThemePolicy;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

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

        $this->assertTrue($policy->viewAny($viewer));
        $this->assertTrue($policy->viewAny($editor));
        $this->assertTrue($policy->viewAny($admin));
    }

    /**
     * Any user regardless of role can view a theme.
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

        $theme = Theme::factory()
            ->for(Anime::factory())
            ->create();
        $policy = new ThemePolicy();

        $this->assertTrue($policy->view($viewer, $theme));
        $this->assertTrue($policy->view($editor, $theme));
        $this->assertTrue($policy->view($admin, $theme));
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

        $this->assertFalse($policy->create($viewer));
        $this->assertTrue($policy->create($editor));
        $this->assertTrue($policy->create($admin));
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

        $theme = Theme::factory()
            ->for(Anime::factory())
            ->create();
        $policy = new ThemePolicy();

        $this->assertFalse($policy->update($viewer, $theme));
        $this->assertTrue($policy->update($editor, $theme));
        $this->assertTrue($policy->update($admin, $theme));
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

        $theme = Theme::factory()
            ->for(Anime::factory())
            ->create();
        $policy = new ThemePolicy();

        $this->assertFalse($policy->delete($viewer, $theme));
        $this->assertTrue($policy->delete($editor, $theme));
        $this->assertTrue($policy->delete($admin, $theme));
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

        $theme = Theme::factory()
            ->for(Anime::factory())
            ->create();
        $policy = new ThemePolicy();

        $this->assertFalse($policy->restore($viewer, $theme));
        $this->assertTrue($policy->restore($editor, $theme));
        $this->assertTrue($policy->restore($admin, $theme));
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

        $theme = Theme::factory()
            ->for(Anime::factory())
            ->create();
        $policy = new ThemePolicy();

        $this->assertFalse($policy->forceDelete($viewer, $theme));
        $this->assertFalse($policy->forceDelete($editor, $theme));
        $this->assertTrue($policy->forceDelete($admin, $theme));
    }
}
