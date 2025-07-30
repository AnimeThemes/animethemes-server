<?php

declare(strict_types=1);

use App\Enums\Auth\CrudPermission;
use App\Models\Admin\Dump;
use App\Models\Auth\User;
use Laravel\Sanctum\Sanctum;

test('protected', function () {
    $dump = Dump::factory()->createOne();

    $response = $this->delete(route('api.dump.destroy', ['dump' => $dump]));

    $response->assertUnauthorized();
});

test('forbidden', function () {
    $dump = Dump::factory()->createOne();

    $user = User::factory()->createOne();

    Sanctum::actingAs($user);

    $response = $this->delete(route('api.dump.destroy', ['dump' => $dump]));

    $response->assertForbidden();
});

test('deleted', function () {
    $dump = Dump::factory()->createOne();

    $user = User::factory()->withPermissions(CrudPermission::DELETE->format(Dump::class))->createOne();

    Sanctum::actingAs($user);

    $response = $this->delete(route('api.dump.destroy', ['dump' => $dump]));

    $response->assertOk();
    static::assertModelMissing($dump);
});
