<?php

declare(strict_types=1);

use App\Enums\Auth\CrudPermission;
use App\Models\Admin\Announcement;
use App\Models\Auth\User;
use Laravel\Sanctum\Sanctum;

use function Pest\Laravel\put;

test('protected', function () {
    $announcement = Announcement::factory()->createOne();

    $parameters = Announcement::factory()->raw();

    $response = put(route('api.announcement.update', ['announcement' => $announcement] + $parameters));

    $response->assertUnauthorized();
});

test('forbidden', function () {
    $announcement = Announcement::factory()->createOne();

    $parameters = Announcement::factory()->raw();

    $user = User::factory()->createOne();

    Sanctum::actingAs($user);

    $response = put(route('api.announcement.update', ['announcement' => $announcement] + $parameters));

    $response->assertForbidden();
});

test('update', function () {
    $announcement = Announcement::factory()->createOne();

    $parameters = Announcement::factory()->raw();

    $user = User::factory()->withPermissions(CrudPermission::UPDATE->format(Announcement::class))->createOne();

    Sanctum::actingAs($user);

    $response = put(route('api.announcement.update', ['announcement' => $announcement] + $parameters));

    $response->assertOk();
});
