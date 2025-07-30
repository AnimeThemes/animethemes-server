<?php

declare(strict_types=1);

use App\Enums\Auth\CrudPermission;
use App\Models\Auth\User;
use App\Models\Wiki\Audio;
use Laravel\Sanctum\Sanctum;

test('protected', function () {
    $audio = Audio::factory()->createOne();

    $parameters = Audio::factory()->raw();

    $response = $this->put(route('api.audio.update', ['audio' => $audio] + $parameters));

    $response->assertUnauthorized();
});

test('forbidden', function () {
    $audio = Audio::factory()->createOne();

    $parameters = Audio::factory()->raw();

    $user = User::factory()->createOne();

    Sanctum::actingAs($user);

    $response = $this->put(route('api.audio.update', ['audio' => $audio] + $parameters));

    $response->assertForbidden();
});

test('trashed', function () {
    $audio = Audio::factory()->trashed()->createOne();

    $parameters = Audio::factory()->raw();

    $user = User::factory()->withPermissions(CrudPermission::UPDATE->format(Audio::class))->createOne();

    Sanctum::actingAs($user);

    $response = $this->put(route('api.audio.update', ['audio' => $audio] + $parameters));

    $response->assertForbidden();
});

test('update', function () {
    $audio = Audio::factory()->createOne();

    $parameters = Audio::factory()->raw();

    $user = User::factory()->withPermissions(CrudPermission::UPDATE->format(Audio::class))->createOne();

    Sanctum::actingAs($user);

    $response = $this->put(route('api.audio.update', ['audio' => $audio] + $parameters));

    $response->assertOk();
});
