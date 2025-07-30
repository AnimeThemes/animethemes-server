<?php

declare(strict_types=1);

use App\Http\Api\Query\Query;
use App\Http\Resources\Auth\Resource\UserResource;
use App\Models\Auth\User;
use Laravel\Sanctum\Sanctum;

uses(Illuminate\Foundation\Testing\WithFaker::class);

test('protected', function () {
    $response = $this->get(route('api.me.show'));

    $response->assertUnauthorized();
});

test('default', function () {
    $user = User::factory()->createOne();

    Sanctum::actingAs($user);

    $response = $this->get(route('api.me.show'));

    $response->assertJson(
        json_decode(
            json_encode(
                new UserResource($user, new Query())
                    ->response()
                    ->getData()
            ),
            true
        )
    );
});
