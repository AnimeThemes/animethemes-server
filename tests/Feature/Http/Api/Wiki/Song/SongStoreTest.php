<?php

declare(strict_types=1);

use App\Enums\Auth\CrudPermission;
use App\Models\Auth\User;
use App\Models\Wiki\Song;
use Laravel\Sanctum\Sanctum;

use function Pest\Laravel\post;

test('protected', function () {
    $song = Song::factory()->makeOne();

    $response = post(route('api.song.store', $song->toArray()));

    $response->assertUnauthorized();
});

test('forbidden', function () {
    $song = Song::factory()->makeOne();

    $user = User::factory()->createOne();

    Sanctum::actingAs($user);

    $response = post(route('api.song.store', $song->toArray()));

    $response->assertForbidden();
});

test('create', function () {
    $parameters = Song::factory()->raw();

    $user = User::factory()->withPermissions(CrudPermission::CREATE->format(Song::class))->createOne();

    Sanctum::actingAs($user);

    $response = post(route('api.song.store', $parameters));

    $response->assertCreated();
    $this->assertDatabaseCount(Song::class, 1);
});
