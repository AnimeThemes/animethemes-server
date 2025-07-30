<?php

declare(strict_types=1);

use App\Models\Auth\Role;
use App\Models\Auth\User;
use Illuminate\Auth\Events\Verified;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Str;

use function Pest\Laravel\actingAs;

use Spatie\Permission\PermissionRegistrar;

uses(Illuminate\Foundation\Testing\WithFaker::class);

test('assigns default roles', function () {
    Event::fakeExcept(Verified::class);

    Collection::times(fake()->randomDigitNotNull, function () {
        Role::findOrCreate(Str::random());
    });

    $defaultRoleCount = fake()->randomDigitNotNull();

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

    actingAs($user)->get($url);

    $this->assertCount($defaultRoleCount, $user->roles()->get());
});
