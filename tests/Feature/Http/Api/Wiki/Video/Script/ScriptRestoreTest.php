<?php

declare(strict_types=1);

use App\Enums\Auth\ExtendedCrudPermission;
use App\Models\Auth\User;
use App\Models\Wiki\Video\VideoScript;
use Laravel\Sanctum\Sanctum;

use function Pest\Laravel\patch;

test('protected', function () {
    $script = VideoScript::factory()->trashed()->createOne();

    $response = patch(route('api.videoscript.restore', ['videoscript' => $script]));

    $response->assertUnauthorized();
});

test('forbidden', function () {
    $script = VideoScript::factory()->trashed()->createOne();

    $user = User::factory()->createOne();

    Sanctum::actingAs($user);

    $response = patch(route('api.videoscript.restore', ['videoscript' => $script]));

    $response->assertForbidden();
});

test('trashed', function () {
    $script = VideoScript::factory()->createOne();

    $user = User::factory()->withPermissions(ExtendedCrudPermission::RESTORE->format(VideoScript::class))->createOne();

    Sanctum::actingAs($user);

    $response = patch(route('api.videoscript.restore', ['videoscript' => $script]));

    $response->assertForbidden();
});

test('restored', function () {
    $script = VideoScript::factory()->trashed()->createOne();

    $user = User::factory()->withPermissions(ExtendedCrudPermission::RESTORE->format(VideoScript::class))->createOne();

    Sanctum::actingAs($user);

    $response = patch(route('api.videoscript.restore', ['videoscript' => $script]));

    $response->assertOk();
    $this->assertNotSoftDeleted($script);
});
