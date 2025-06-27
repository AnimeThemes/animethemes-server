<?php

declare(strict_types=1);

namespace Tests\Feature\Http\Auth;

use App\Models\Auth\Role;
use App\Models\Auth\User;
use Illuminate\Auth\Events\Verified;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Str;
use Spatie\Permission\PermissionRegistrar;
use Tests\TestCase;

/**
 * Class EmailVerificationTest.
 */
class EmailVerificationTest extends TestCase
{
    use WithFaker;

    /**
     * The Email Verification route shall assign default roles.
     *
     * @return void
     */
    public function test_assigns_default_roles(): void
    {
        Event::fakeExcept(Verified::class);

        Collection::times($this->faker->randomDigitNotNull, function () {
            Role::findOrCreate(Str::random());
        });

        $defaultRoleCount = $this->faker->randomDigitNotNull();

        Collection::times($defaultRoleCount, function () {
            /** @var Role $role */
            $role = Role::findOrCreate(Str::random());

            $role->default = true;
            $role->save();
        });

        App::make(PermissionRegistrar::class)->forgetCachedPermissions();

        $user = User::factory()->createOne([
            User::ATTRIBUTE_EMAIL_VERIFIED_AT => null,
        ]);

        $url = URL::temporarySignedRoute(
            'verification.verify',
            now()->addMinutes(60),
            [
                'id' => $user->getKey(),
                'hash' => sha1($user->email),
            ]
        );

        $this->actingAs($user)->get($url);

        static::assertCount($defaultRoleCount, $user->roles()->get());
    }
}
