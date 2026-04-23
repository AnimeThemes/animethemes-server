<?php

declare(strict_types=1);

use App\Http\Api\Query\Query;
use App\Http\Resources\Auth\Resource\UserJsonResource;
use App\Models\Auth\User;
use Illuminate\Foundation\Testing\WithFaker;
use Laravel\Sanctum\Sanctum;

use function Pest\Laravel\get;

uses(WithFaker::class);

test('protected', function (): void {
    $response = get(route('api.me.show'));

    $response->assertUnauthorized();
});

test('default', function (): void {
    $user = User::factory()->createOne();

    Sanctum::actingAs($user);

    $response = get(route('api.me.show'));

    $response->assertJson(
        json_decode(
            json_encode(
                new UserJsonResource($user, new Query())
                    ->response()
                    ->getData()
            ),
            true
        )
    );
});
