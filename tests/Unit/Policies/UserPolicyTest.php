<?php

namespace Tests\Unit\Policies;

use App\Enums\UserRole;
use App\Models\User;
use App\Policies\UserPolicy;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class UserPolicyTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    /**
     * Any user regardless of role can view any user.
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

        $policy = new UserPolicy();

        $this->assertTrue($policy->viewAny($read_only));
        $this->assertTrue($policy->viewAny($contributor));
        $this->assertTrue($policy->viewAny($admin));
    }

    /**
     * Any user regardless of role can view a user.
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

        $user = User::factory()->create();
        $policy = new UserPolicy();

        $this->assertFalse($policy->view($read_only, $user));
        $this->assertFalse($policy->view($contributor, $user));
        $this->assertTrue($policy->view($admin, $user));
        $this->assertTrue($policy->view($user, $user));
    }

    /**
     * A contributor or admin may create a user.
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

        $policy = new UserPolicy();

        $this->assertFalse($policy->create($read_only));
        $this->assertFalse($policy->create($contributor));
        $this->assertTrue($policy->create($admin));
    }

    /**
     * A contributor or admin may update a user.
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

        $user = User::factory()->create();
        $policy = new UserPolicy();

        $this->assertFalse($policy->update($read_only, $user));
        $this->assertFalse($policy->update($contributor, $user));
        $this->assertTrue($policy->update($admin, $user));
    }

    /**
     * A contributor or admin may delete a user.
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

        $user = User::factory()->create();
        $policy = new UserPolicy();

        $this->assertFalse($policy->delete($read_only, $user));
        $this->assertFalse($policy->delete($contributor, $user));
        $this->assertTrue($policy->delete($admin, $user));
    }

    /**
     * A contributor or admin may restore a user.
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

        $user = User::factory()->create();
        $policy = new UserPolicy();

        $this->assertFalse($policy->restore($read_only, $user));
        $this->assertFalse($policy->restore($contributor, $user));
        $this->assertTrue($policy->restore($admin, $user));
    }

    /**
     * A contributor or admin may force delete a user.
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

        $user = User::factory()->create();
        $policy = new UserPolicy();

        $this->assertFalse($policy->forceDelete($read_only, $user));
        $this->assertFalse($policy->forceDelete($contributor, $user));
        $this->assertTrue($policy->forceDelete($admin, $user));
    }
}
