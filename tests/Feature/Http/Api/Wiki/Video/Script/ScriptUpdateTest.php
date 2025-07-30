<?php

declare(strict_types=1);

use App\Enums\Auth\CrudPermission;
use App\Models\Auth\User;
use App\Models\Wiki\Video\VideoScript;
use Laravel\Sanctum\Sanctum;

use function Pest\Laravel\put;

test('protected', function () {
    $script = VideoScript::factory()->createOne();

    $parameters = VideoScript::factory()->raw();

    $response = put(route('api.videoscript.update', ['videoscript' => $script] + $parameters));

    $response->assertUnauthorized();
});

test('forbidden', function () {
    $script = VideoScript::factory()->createOne();

    $parameters = VideoScript::factory()->raw();

    $user = User::factory()->createOne();

    Sanctum::actingAs($user);

    $response = put(route('api.videoscript.update', ['videoscript' => $script] + $parameters));

    $response->assertForbidden();
});

test('trashed', function () {
    $script = VideoScript::factory()->trashed()->createOne();

    $parameters = VideoScript::factory()->raw();

    $user = User::factory()->withPermissions(CrudPermission::UPDATE->format(VideoScript::class))->createOne();

    Sanctum::actingAs($user);

    $response = put(route('api.videoscript.update', ['videoscript' => $script] + $parameters));

    $response->assertForbidden();
});

test('update', function () {
    $script = VideoScript::factory()->createOne();

    $parameters = VideoScript::factory()->raw();

    $user = User::factory()->withPermissions(CrudPermission::UPDATE->format(VideoScript::class))->createOne();

    Sanctum::actingAs($user);

    $response = put(route('api.videoscript.update', ['videoscript' => $script] + $parameters));

    $response->assertOk();
});
