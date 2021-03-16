<?php

namespace Tests\Unit\Models;

use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\WithoutEvents;
use Illuminate\Support\Facades\Notification;
use Laravel\Sanctum\PersonalAccessToken;
use Tests\TestCase;

class UserTest extends TestCase
{
    use RefreshDatabase, WithFaker, WithoutEvents;

    /**
     * The role attribute of a user shall be cast to a UserRole enum instance.
     *
     * @return void
     */
    public function testCastsRoleToEnum()
    {
        $user = User::factory()->create();

        $role = $user->role;

        $this->assertInstanceOf(UserRole::class, $role);
    }

    /**
     * Users shall have a one-to-many polymorphic relationship to PersonalAccessToken.
     *
     * @return void
     */
    public function testTokens()
    {
        $user = User::factory()->create();

        $user->createToken($this->faker->word());

        $this->assertInstanceOf(MorphMany::class, $user->tokens());
        $this->assertEquals(1, $user->tokens()->count());
        $this->assertInstanceOf(PersonalAccessToken::class, $user->tokens()->first());
    }

    /**
     * Users shall verify email.
     *
     * @return void
     */
    public function testVerificationEmailNotification()
    {
        Notification::fake();

        $user = User::factory()->create();

        $user->sendEmailVerificationNotification();

        Notification::assertSentTo($user, VerifyEmail::class);
    }

    /**
     * Users shall be nameable.
     *
     * @return void
     */
    public function testNameable()
    {
        $user = User::factory()->create();

        $this->assertIsString($user->getName());
    }

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
