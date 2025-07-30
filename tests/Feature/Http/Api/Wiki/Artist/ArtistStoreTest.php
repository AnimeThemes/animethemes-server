<?php

declare(strict_types=1);

use App\Enums\Auth\CrudPermission;
use App\Models\Auth\User;
use App\Models\Wiki\Artist;
use Laravel\Sanctum\Sanctum;

test('protected', function () {
    $artist = Artist::factory()->makeOne();

    $response = $this->post(route('api.artist.store', $artist->toArray()));

    $response->assertUnauthorized();
});

test('forbidden', function () {
    $artist = Artist::factory()->makeOne();

    $user = User::factory()->createOne();

    Sanctum::actingAs($user);

    $response = $this->post(route('api.artist.store', $artist->toArray()));

    $response->assertForbidden();
});

test('required fields', function () {
    $user = User::factory()->withPermissions(CrudPermission::CREATE->format(Artist::class))->createOne();

    Sanctum::actingAs($user);

    $response = $this->post(route('api.artist.store'));

    $response->assertJsonValidationErrors([
        Artist::ATTRIBUTE_NAME,
        Artist::ATTRIBUTE_SLUG,
    ]);
});

test('create', function () {
    $parameters = Artist::factory()->raw();

    $user = User::factory()->withPermissions(CrudPermission::CREATE->format(Artist::class))->createOne();

    Sanctum::actingAs($user);

    $response = $this->post(route('api.artist.store', $parameters));

    $response->assertCreated();
    static::assertDatabaseCount(Artist::class, 1);
});
