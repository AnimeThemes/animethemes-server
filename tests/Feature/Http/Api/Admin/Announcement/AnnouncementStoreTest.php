<?php

declare(strict_types=1);

use App\Enums\Auth\CrudPermission;
use App\Models\Admin\Announcement;
use App\Models\Auth\User;
use Laravel\Sanctum\Sanctum;

use function Pest\Laravel\post;

test('protected', function () {
    $announcement = Announcement::factory()->makeOne();

    $response = post(route('api.announcement.store', $announcement->toArray()));

    $response->assertUnauthorized();
});

test('forbidden', function () {
    $announcement = Announcement::factory()->makeOne();

    $user = User::factory()->createOne();

    Sanctum::actingAs($user);

    $response = post(route('api.announcement.store', $announcement->toArray()));

    $response->assertForbidden();
});

test('required fields', function () {
    $user = User::factory()->withPermissions(CrudPermission::CREATE->format(Announcement::class))->createOne();

    Sanctum::actingAs($user);

    $response = post(route('api.announcement.store'));

    $response->assertJsonValidationErrors([
        Announcement::ATTRIBUTE_CONTENT,
    ]);
});

test('create', function () {
    $parameters = Announcement::factory()->raw();

    $user = User::factory()->withPermissions(CrudPermission::CREATE->format(Announcement::class))->createOne();

    Sanctum::actingAs($user);

    $response = post(route('api.announcement.store', $parameters));

    $response->assertCreated();
    $this->assertDatabaseCount(Announcement::class, 1);
});
