<?php

declare(strict_types=1);

use App\Enums\Auth\ExtendedCrudPermission;
use App\Models\Auth\User;
use App\Models\Wiki\Group;
use Laravel\Sanctum\Sanctum;

use function Pest\Laravel\patch;

test('protected', function (): void {
    $group = Group::factory()->trashed()->createOne();

    $response = patch(route('api.group.restore', ['group' => $group]));

    $response->assertUnauthorized();
});

test('forbidden', function (): void {
    $group = Group::factory()->trashed()->createOne();

    $user = User::factory()->createOne();

    Sanctum::actingAs($user);

    $response = patch(route('api.group.restore', ['group' => $group]));

    $response->assertForbidden();
});

test('trashed', function (): void {
    $group = Group::factory()->createOne();

    $user = User::factory()->withPermissions(ExtendedCrudPermission::RESTORE->format(Group::class))->createOne();

    Sanctum::actingAs($user);

    $response = patch(route('api.group.restore', ['group' => $group]));

    $response->assertOk();
});

test('restored', function (): void {
    $group = Group::factory()->trashed()->createOne();

    $user = User::factory()->withPermissions(ExtendedCrudPermission::RESTORE->format(Group::class))->createOne();

    Sanctum::actingAs($user);

    $response = patch(route('api.group.restore', ['group' => $group]));

    $response->assertOk();
    $this->assertNotSoftDeleted($group);
});
