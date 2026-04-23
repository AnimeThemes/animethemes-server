<?php

declare(strict_types=1);

use App\Enums\Auth\CrudPermission;
use App\Models\Auth\User;
use App\Models\Wiki\Group;
use Laravel\Sanctum\Sanctum;

use function Pest\Laravel\delete;

test('protected', function (): void {
    $group = Group::factory()->createOne();

    $response = delete(route('api.group.destroy', ['group' => $group]));

    $response->assertUnauthorized();
});

test('forbidden', function (): void {
    $group = Group::factory()->createOne();

    $user = User::factory()->createOne();

    Sanctum::actingAs($user);

    $response = delete(route('api.group.destroy', ['group' => $group]));

    $response->assertForbidden();
});

test('trashed', function (): void {
    $group = Group::factory()->trashed()->createOne();

    $user = User::factory()->withPermissions(CrudPermission::DELETE->format(Group::class))->createOne();

    Sanctum::actingAs($user);

    $response = delete(route('api.group.destroy', ['group' => $group]));

    $response->assertNotFound();
});

test('deleted', function (): void {
    $group = Group::factory()->createOne();

    $user = User::factory()->withPermissions(CrudPermission::DELETE->format(Group::class))->createOne();

    Sanctum::actingAs($user);

    $response = delete(route('api.group.destroy', ['group' => $group]));

    $response->assertOk();
    $this->assertSoftDeleted($group);
});
