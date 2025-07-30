<?php

declare(strict_types=1);

use App\Enums\Auth\CrudPermission;
use App\Models\Admin\Dump;
use App\Models\Auth\User;
use Laravel\Sanctum\Sanctum;

use function Pest\Laravel\post;

test('protected', function () {
    $dump = Dump::factory()->makeOne();

    $response = post(route('api.dump.store', $dump->toArray()));

    $response->assertUnauthorized();
});

test('forbidden', function () {
    $dump = Dump::factory()->makeOne();

    $user = User::factory()->createOne();

    Sanctum::actingAs($user);

    $response = post(route('api.dump.store', $dump->toArray()));

    $response->assertForbidden();
});

test('required fields', function () {
    $user = User::factory()->withPermissions(CrudPermission::CREATE->format(Dump::class))->createOne();

    Sanctum::actingAs($user);

    $response = post(route('api.dump.store'));

    $response->assertJsonValidationErrors([
        Dump::ATTRIBUTE_PATH,
    ]);
});

test('create', function () {
    $parameters = Dump::factory()->raw();

    $user = User::factory()->withPermissions(CrudPermission::CREATE->format(Dump::class))->createOne();

    Sanctum::actingAs($user);

    $response = post(route('api.dump.store', $parameters));

    $response->assertCreated();
    $this->assertDatabaseCount(Dump::class, 1);
});
