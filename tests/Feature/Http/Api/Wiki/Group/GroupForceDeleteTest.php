<?php

declare(strict_types=1);

use App\Enums\Auth\ExtendedCrudPermission;
use App\Models\Auth\User;
use App\Models\Wiki\Group;
use Laravel\Sanctum\Sanctum;

use function Pest\Laravel\delete;

test('protected', function () {
    $group = Group::factory()->createOne();

    $response = delete(route('api.group.forceDelete', ['group' => $group]));

    $response->assertUnauthorized();
});

test('forbidden', function () {
    $group = Group::factory()->createOne();

    $user = User::factory()->createOne();

    Sanctum::actingAs($user);

    $response = delete(route('api.group.forceDelete', ['group' => $group]));

    $response->assertForbidden();
});

test('deleted', function () {
    $group = Group::factory()->createOne();

    $user = User::factory()->withPermissions(ExtendedCrudPermission::FORCE_DELETE->format(Group::class))->createOne();

    Sanctum::actingAs($user);

    $response = delete(route('api.group.forceDelete', ['group' => $group]));

    $response->assertOk();
    $this->assertModelMissing($group);
});
