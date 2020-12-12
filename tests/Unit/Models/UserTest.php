<?php

namespace Tests\Unit\Models;

use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class UserTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    /**
     * An Admin user shall have the Admin role.
     *
     * @return void
     */
    public function testUserRoleIsAdmin()
    {
        $user = User::factory()->create([
            'role' => UserRole::ADMIN,
        ]);

        $this->assertTrue($user->isAdmin());
        $this->assertFalse($user->isContributor());
        $this->assertFalse($user->isReadOnly());
    }

    /**
     * A Contributor user shall have the Contributor role.
     *
     * @return void
     */
    public function testUserRoleIsContributor()
    {
        $user = User::factory()->create([
            'role' => UserRole::CONTRIBUTOR,
        ]);

        $this->assertFalse($user->isAdmin());
        $this->assertTrue($user->isContributor());
        $this->assertFalse($user->isReadOnly());
    }

    /**
     * A Read-only user shall have the Read-only role.
     *
     * @return void
     */
    public function testUserRoleIsReadOnly()
    {
        $user = User::factory()->create([
            'role' => UserRole::READ_ONLY,
        ]);

        $this->assertFalse($user->isAdmin());
        $this->assertFalse($user->isContributor());
        $this->assertTrue($user->isReadOnly());
    }
}
