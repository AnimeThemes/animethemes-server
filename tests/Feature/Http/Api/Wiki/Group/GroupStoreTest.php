<?php

declare(strict_types=1);

use App\Enums\Auth\CrudPermission;
use App\Models\Auth\User;
use App\Models\Wiki\Group;
use Laravel\Sanctum\Sanctum;

use function Pest\Laravel\post;

test('protected', function () {
    $group = Group::factory()->makeOne();

    $response = post(route('api.group.store', $group->toArray()));

    $response->assertUnauthorized();
});

test('forbidden', function () {
    $group = Group::factory()->makeOne();

    $user = User::factory()->createOne();

    Sanctum::actingAs($user);

    $response = post(route('api.group.store', $group->toArray()));

    $response->assertForbidden();
});

test('create', function () {
    $parameters = Group::factory()->raw();

    $user = User::factory()->withPermissions(CrudPermission::CREATE->format(Group::class))->createOne();

    Sanctum::actingAs($user);

    $response = post(route('api.group.store', $parameters));

    $response->assertCreated();
    $this->assertDatabaseCount(Group::class, 1);
});
