<?php

declare(strict_types=1);

use App\Enums\Auth\CrudPermission;
use App\Models\Auth\User;
use App\Models\Wiki\Audio;
use Laravel\Sanctum\Sanctum;

test('protected', function () {
    $audio = Audio::factory()->makeOne();

    $response = $this->post(route('api.audio.store', $audio->toArray()));

    $response->assertUnauthorized();
});

test('forbidden', function () {
    $audio = Audio::factory()->makeOne();

    $user = User::factory()->createOne();

    Sanctum::actingAs($user);

    $response = $this->post(route('api.audio.store', $audio->toArray()));

    $response->assertForbidden();
});

test('required fields', function () {
    $user = User::factory()->withPermissions(CrudPermission::CREATE->format(Audio::class))->createOne();

    Sanctum::actingAs($user);

    $response = $this->post(route('api.audio.store'));

    $response->assertJsonValidationErrors([
        Audio::ATTRIBUTE_BASENAME,
        Audio::ATTRIBUTE_FILENAME,
        Audio::ATTRIBUTE_MIMETYPE,
        Audio::ATTRIBUTE_PATH,
        Audio::ATTRIBUTE_SIZE,
    ]);
});

test('create', function () {
    $parameters = Audio::factory()->raw();

    $user = User::factory()->withPermissions(CrudPermission::CREATE->format(Audio::class))->createOne();

    Sanctum::actingAs($user);

    $response = $this->post(route('api.audio.store', $parameters));

    $response->assertCreated();
    static::assertDatabaseCount(Audio::class, 1);
});
