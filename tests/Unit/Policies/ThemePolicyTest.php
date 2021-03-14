<?php

namespace Tests\Unit\Policies;

use App\Enums\UserRole;
use App\Models\Anime;
use App\Models\Theme;
use App\Models\User;
use App\Policies\ThemePolicy;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class ThemePolicyTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    /**
     * Any user regardless of role can view any theme.
     *
     * @return void
     */
    public function testViewAny()
    {
        $read_only = User::factory()->create([
            'role' => UserRole::READ_ONLY,
        ]);

        $contributor = User::factory()->create([
            'role' => UserRole::CONTRIBUTOR,
        ]);

        $admin = User::factory()->create([
            'role' => UserRole::ADMIN,
        ]);

        $policy = new ThemePolicy();

        $this->assertTrue($policy->viewAny($read_only));
        $this->assertTrue($policy->viewAny($contributor));
        $this->assertTrue($policy->viewAny($admin));
    }

    /**
     * Any user regardless of role can view a theme.
     *
     * @return void
     */
    public function testView()
    {
        $read_only = User::factory()->create([
            'role' => UserRole::READ_ONLY,
        ]);

        $contributor = User::factory()->create([
            'role' => UserRole::CONTRIBUTOR,
        ]);

        $admin = User::factory()->create([
            'role' => UserRole::ADMIN,
        ]);

        $theme = Theme::factory()
            ->for(Anime::factory())
            ->create();
        $policy = new ThemePolicy();

        $this->assertTrue($policy->view($read_only, $theme));
        $this->assertTrue($policy->view($contributor, $theme));
        $this->assertTrue($policy->view($admin, $theme));
    }

    /**
     * A contributor or admin may create a theme.
     *
     * @return void
     */
    public function testCreate()
    {
        $read_only = User::factory()->create([
            'role' => UserRole::READ_ONLY,
        ]);

        $contributor = User::factory()->create([
            'role' => UserRole::CONTRIBUTOR,
        ]);

        $admin = User::factory()->create([
            'role' => UserRole::ADMIN,
        ]);

        $policy = new ThemePolicy();

        $this->assertFalse($policy->create($read_only));
        $this->assertTrue($policy->create($contributor));
        $this->assertTrue($policy->create($admin));
    }

    /**
     * A contributor or admin may update a theme.
     *
     * @return void
     */
    public function testUpdate()
    {
        $read_only = User::factory()->create([
            'role' => UserRole::READ_ONLY,
        ]);

        $contributor = User::factory()->create([
            'role' => UserRole::CONTRIBUTOR,
        ]);

        $admin = User::factory()->create([
            'role' => UserRole::ADMIN,
        ]);

        $theme = Theme::factory()
            ->for(Anime::factory())
            ->create();
        $policy = new ThemePolicy();

        $this->assertFalse($policy->update($read_only, $theme));
        $this->assertTrue($policy->update($contributor, $theme));
        $this->assertTrue($policy->update($admin, $theme));
    }

    /**
     * A contributor or admin may delete a theme.
     *
     * @return void
     */
    public function testDelete()
    {
        $read_only = User::factory()->create([
            'role' => UserRole::READ_ONLY,
        ]);

        $contributor = User::factory()->create([
            'role' => UserRole::CONTRIBUTOR,
        ]);

        $admin = User::factory()->create([
            'role' => UserRole::ADMIN,
        ]);

        $theme = Theme::factory()
            ->for(Anime::factory())
            ->create();
        $policy = new ThemePolicy();

        $this->assertFalse($policy->delete($read_only, $theme));
        $this->assertTrue($policy->delete($contributor, $theme));
        $this->assertTrue($policy->delete($admin, $theme));
    }

    /**
     * A contributor or admin may restore a theme.
     *
     * @return void
     */
    public function testRestore()
    {
        $read_only = User::factory()->create([
            'role' => UserRole::READ_ONLY,
        ]);

        $contributor = User::factory()->create([
            'role' => UserRole::CONTRIBUTOR,
        ]);

        $admin = User::factory()->create([
            'role' => UserRole::ADMIN,
        ]);

        $theme = Theme::factory()
            ->for(Anime::factory())
            ->create();
        $policy = new ThemePolicy();

        $this->assertFalse($policy->restore($read_only, $theme));
        $this->assertTrue($policy->restore($contributor, $theme));
        $this->assertTrue($policy->restore($admin, $theme));
    }

    /**
     * A contributor or admin may force delete a theme.
     *
     * @return void
     */
    public function testForceDelete()
    {
        $read_only = User::factory()->create([
            'role' => UserRole::READ_ONLY,
        ]);

        $contributor = User::factory()->create([
            'role' => UserRole::CONTRIBUTOR,
        ]);

        $admin = User::factory()->create([
            'role' => UserRole::ADMIN,
        ]);

        $theme = Theme::factory()
            ->for(Anime::factory())
            ->create();
        $policy = new ThemePolicy();

        $this->assertFalse($policy->forceDelete($read_only, $theme));
        $this->assertFalse($policy->forceDelete($contributor, $theme));
        $this->assertTrue($policy->forceDelete($admin, $theme));
    }
}
