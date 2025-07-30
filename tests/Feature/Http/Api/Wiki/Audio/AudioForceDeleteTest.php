<?php

declare(strict_types=1);

use App\Enums\Auth\ExtendedCrudPermission;
use App\Models\Auth\User;
use App\Models\Wiki\Audio;
use Laravel\Sanctum\Sanctum;

test('protected', function () {
    $audio = Audio::factory()->createOne();

    $response = $this->delete(route('api.audio.forceDelete', ['audio' => $audio]));

    $response->assertUnauthorized();
});

test('forbidden', function () {
    $audio = Audio::factory()->createOne();

    $user = User::factory()->createOne();

    Sanctum::actingAs($user);

    $response = $this->delete(route('api.audio.forceDelete', ['audio' => $audio]));

    $response->assertForbidden();
});

test('deleted', function () {
    $audio = Audio::factory()->createOne();

    $user = User::factory()->withPermissions(ExtendedCrudPermission::FORCE_DELETE->format(Audio::class))->createOne();

    Sanctum::actingAs($user);

    $response = $this->delete(route('api.audio.forceDelete', ['audio' => $audio]));

    $response->assertOk();
    static::assertModelMissing($audio);
});
