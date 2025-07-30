<?php

declare(strict_types=1);

use App\Enums\Auth\CrudPermission;
use App\Http\Api\Query\Query;
use App\Http\Resources\List\Collection\ExternalProfileCollection;
use App\Models\Auth\User;
use App\Models\List\ExternalProfile;
use Laravel\Sanctum\Sanctum;

use function Pest\Laravel\get;

uses(Illuminate\Foundation\Testing\WithFaker::class);

test('protected', function () {
    $response = get(route('api.me.externalprofile.index'));

    $response->assertUnauthorized();
});

test('forbidden if missing permission', function () {
    $user = User::factory()->createOne();

    Sanctum::actingAs($user);

    $response = get(route('api.me.externalprofile.index'));

    $response->assertForbidden();
});

test('only sees owned profiles', function () {
    ExternalProfile::factory()
        ->for(User::factory())
        ->count(fake()->randomDigitNotNull())
        ->create();

    ExternalProfile::factory()
        ->count(fake()->randomDigitNotNull())
        ->create();

    $user = User::factory()->withPermissions(CrudPermission::VIEW->format(ExternalProfile::class))->createOne();

    $profileCount = fake()->randomDigitNotNull();

    $profiles = ExternalProfile::factory()
        ->for($user)
        ->count($profileCount)
        ->create();

    Sanctum::actingAs($user);

    $response = get(route('api.me.externalprofile.index'));

    $response->assertJsonCount($profileCount, ExternalProfileCollection::$wrap);

    $response->assertJson(
        json_decode(
            json_encode(
                new ExternalProfileCollection($profiles, new Query())
                    ->response()
                    ->getData()
            ),
            true
        )
    );
});
