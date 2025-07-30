<?php

declare(strict_types=1);

use App\Enums\Auth\ExtendedCrudPermission;
use App\Models\Auth\User;
use App\Models\Wiki\Video\VideoScript;
use Laravel\Sanctum\Sanctum;

use function Pest\Laravel\delete;

test('protected', function () {
    $script = VideoScript::factory()->createOne();

    $response = delete(route('api.videoscript.forceDelete', ['videoscript' => $script]));

    $response->assertUnauthorized();
});

test('forbidden', function () {
    $script = VideoScript::factory()->createOne();

    $user = User::factory()->createOne();

    Sanctum::actingAs($user);

    $response = delete(route('api.videoscript.forceDelete', ['videoscript' => $script]));

    $response->assertForbidden();
});

test('deleted', function () {
    $script = VideoScript::factory()->createOne();

    $user = User::factory()->withPermissions(ExtendedCrudPermission::FORCE_DELETE->format(VideoScript::class))->createOne();

    Sanctum::actingAs($user);

    $response = delete(route('api.videoscript.forceDelete', ['videoscript' => $script]));

    $response->assertOk();
    $this->assertModelMissing($script);
});
