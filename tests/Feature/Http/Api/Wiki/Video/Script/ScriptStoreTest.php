<?php

declare(strict_types=1);

use App\Enums\Auth\CrudPermission;
use App\Models\Auth\User;
use App\Models\Wiki\Video\VideoScript;
use Laravel\Sanctum\Sanctum;

test('protected', function () {
    $script = VideoScript::factory()->makeOne();

    $response = $this->post(route('api.videoscript.store', $script->toArray()));

    $response->assertUnauthorized();
});

test('forbidden', function () {
    $script = VideoScript::factory()->makeOne();

    $user = User::factory()->createOne();

    Sanctum::actingAs($user);

    $response = $this->post(route('api.videoscript.store', $script->toArray()));

    $response->assertForbidden();
});

test('required fields', function () {
    $user = User::factory()->withPermissions(CrudPermission::CREATE->format(VideoScript::class))->createOne();

    Sanctum::actingAs($user);

    $response = $this->post(route('api.videoscript.store'));

    $response->assertJsonValidationErrors([
        VideoScript::ATTRIBUTE_PATH,
    ]);
});

test('create', function () {
    $parameters = VideoScript::factory()->raw();

    $user = User::factory()->withPermissions(CrudPermission::CREATE->format(VideoScript::class))->createOne();

    Sanctum::actingAs($user);

    $response = $this->post(route('api.videoscript.store', $parameters));

    $response->assertCreated();
    static::assertDatabaseCount(VideoScript::class, 1);
});
