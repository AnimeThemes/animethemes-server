<?php

declare(strict_types=1);

use App\Enums\Auth\CrudPermission;
use App\Models\Admin\Dump;
use App\Models\Auth\User;
use Laravel\Sanctum\Sanctum;

test('protected', function () {
    $dump = Dump::factory()->createOne();

    $parameters = Dump::factory()->raw();

    $response = $this->put(route('api.dump.update', ['dump' => $dump] + $parameters));

    $response->assertUnauthorized();
});

test('forbidden', function () {
    $dump = Dump::factory()->createOne();

    $parameters = Dump::factory()->raw();

    $user = User::factory()->createOne();

    Sanctum::actingAs($user);

    $response = $this->put(route('api.dump.update', ['dump' => $dump] + $parameters));

    $response->assertForbidden();
});

test('update', function () {
    $dump = Dump::factory()->createOne();

    $parameters = Dump::factory()->raw();

    $user = User::factory()->withPermissions(CrudPermission::UPDATE->format(Dump::class))->createOne();

    Sanctum::actingAs($user);

    $response = $this->put(route('api.dump.update', ['dump' => $dump] + $parameters));

    $response->assertOk();
});
