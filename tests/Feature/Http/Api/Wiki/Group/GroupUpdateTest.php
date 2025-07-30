<?php

declare(strict_types=1);

use App\Enums\Auth\CrudPermission;
use App\Models\Auth\User;
use App\Models\Wiki\Group;
use Laravel\Sanctum\Sanctum;

use function Pest\Laravel\put;

test('protected', function () {
    $group = Group::factory()->createOne();

    $parameters = Group::factory()->raw();

    $response = put(route('api.group.update', ['group' => $group] + $parameters));

    $response->assertUnauthorized();
});

test('forbidden', function () {
    $group = Group::factory()->createOne();

    $parameters = Group::factory()->raw();

    $user = User::factory()->createOne();

    Sanctum::actingAs($user);

    $response = put(route('api.group.update', ['group' => $group] + $parameters));

    $response->assertForbidden();
});

test('trashed', function () {
    $group = Group::factory()->trashed()->createOne();

    $parameters = Group::factory()->raw();

    $user = User::factory()->withPermissions(CrudPermission::UPDATE->format(Group::class))->createOne();

    Sanctum::actingAs($user);

    $response = put(route('api.group.update', ['group' => $group] + $parameters));

    $response->assertForbidden();
});

test('update', function () {
    $group = Group::factory()->createOne();

    $parameters = Group::factory()->raw();

    $user = User::factory()->withPermissions(CrudPermission::UPDATE->format(Group::class))->createOne();

    Sanctum::actingAs($user);

    $response = put(route('api.group.update', ['group' => $group] + $parameters));

    $response->assertOk();
});
