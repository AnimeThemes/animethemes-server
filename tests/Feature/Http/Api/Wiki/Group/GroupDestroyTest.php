<?php

declare(strict_types=1);

use App\Enums\Auth\CrudPermission;
use App\Models\Auth\User;
use App\Models\Wiki\Group;
use Laravel\Sanctum\Sanctum;

test('protected', function () {
    $group = Group::factory()->createOne();

    $response = $this->delete(route('api.group.destroy', ['group' => $group]));

    $response->assertUnauthorized();
});

test('forbidden', function () {
    $group = Group::factory()->createOne();

    $user = User::factory()->createOne();

    Sanctum::actingAs($user);

    $response = $this->delete(route('api.group.destroy', ['group' => $group]));

    $response->assertForbidden();
});

test('trashed', function () {
    $group = Group::factory()->trashed()->createOne();

    $user = User::factory()->withPermissions(CrudPermission::DELETE->format(Group::class))->createOne();

    Sanctum::actingAs($user);

    $response = $this->delete(route('api.group.destroy', ['group' => $group]));

    $response->assertNotFound();
});

test('deleted', function () {
    $group = Group::factory()->createOne();

    $user = User::factory()->withPermissions(CrudPermission::DELETE->format(Group::class))->createOne();

    Sanctum::actingAs($user);

    $response = $this->delete(route('api.group.destroy', ['group' => $group]));

    $response->assertOk();
    static::assertSoftDeleted($group);
});
