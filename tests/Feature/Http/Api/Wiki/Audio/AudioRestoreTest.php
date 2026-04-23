<?php

declare(strict_types=1);

use App\Enums\Auth\ExtendedCrudPermission;
use App\Models\Auth\User;
use App\Models\Wiki\Audio;
use Laravel\Sanctum\Sanctum;

use function Pest\Laravel\patch;

test('protected', function (): void {
    $audio = Audio::factory()->trashed()->createOne();

    $response = patch(route('api.audio.restore', ['audio' => $audio]));

    $response->assertUnauthorized();
});

test('forbidden', function (): void {
    $audio = Audio::factory()->trashed()->createOne();

    $user = User::factory()->createOne();

    Sanctum::actingAs($user);

    $response = patch(route('api.audio.restore', ['audio' => $audio]));

    $response->assertForbidden();
});

test('trashed', function (): void {
    $audio = Audio::factory()->createOne();

    $user = User::factory()->withPermissions(ExtendedCrudPermission::RESTORE->format(Audio::class))->createOne();

    Sanctum::actingAs($user);

    $response = patch(route('api.audio.restore', ['audio' => $audio]));

    $response->assertOk();
});

test('restored', function (): void {
    $audio = Audio::factory()->trashed()->createOne();

    $user = User::factory()->withPermissions(ExtendedCrudPermission::RESTORE->format(Audio::class))->createOne();

    Sanctum::actingAs($user);

    $response = patch(route('api.audio.restore', ['audio' => $audio]));

    $response->assertOk();
    $this->assertNotSoftDeleted($audio);
});
