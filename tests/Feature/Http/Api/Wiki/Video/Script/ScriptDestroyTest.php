<?php

declare(strict_types=1);

use App\Enums\Auth\CrudPermission;
use App\Models\Auth\User;
use App\Models\Wiki\Video\VideoScript;
use Laravel\Sanctum\Sanctum;

test('protected', function () {
    $script = VideoScript::factory()->createOne();

    $response = $this->delete(route('api.videoscript.destroy', ['videoscript' => $script]));

    $response->assertUnauthorized();
});

test('forbidden', function () {
    $script = VideoScript::factory()->createOne();

    $user = User::factory()->createOne();

    Sanctum::actingAs($user);

    $response = $this->delete(route('api.videoscript.destroy', ['videoscript' => $script]));

    $response->assertForbidden();
});

test('trashed', function () {
    $script = VideoScript::factory()->trashed()->createOne();

    $user = User::factory()->withPermissions(CrudPermission::DELETE->format(VideoScript::class))->createOne();

    Sanctum::actingAs($user);

    $response = $this->delete(route('api.videoscript.destroy', ['videoscript' => $script]));

    $response->assertNotFound();
});

test('deleted', function () {
    $script = VideoScript::factory()->createOne();

    $user = User::factory()->withPermissions(CrudPermission::DELETE->format(VideoScript::class))->createOne();

    Sanctum::actingAs($user);

    $response = $this->delete(route('api.videoscript.destroy', ['videoscript' => $script]));

    $response->assertOk();
    static::assertSoftDeleted($script);
});
