<?php

declare(strict_types=1);

use App\Enums\Auth\CrudPermission;
use App\Models\Admin\Announcement;
use App\Models\Auth\User;
use Laravel\Sanctum\Sanctum;

use function Pest\Laravel\delete;

test('protected', function () {
    $announcement = Announcement::factory()->createOne();

    $response = delete(route('api.announcement.destroy', ['announcement' => $announcement]));

    $response->assertUnauthorized();
});

test('forbidden', function () {
    $announcement = Announcement::factory()->createOne();

    $user = User::factory()->createOne();

    Sanctum::actingAs($user);

    $response = delete(route('api.announcement.destroy', ['announcement' => $announcement]));

    $response->assertForbidden();
});

test('deleted', function () {
    $announcement = Announcement::factory()->createOne();

    $user = User::factory()->withPermissions(CrudPermission::DELETE->format(Announcement::class))->createOne();

    Sanctum::actingAs($user);

    $response = delete(route('api.announcement.destroy', ['announcement' => $announcement]));

    $response->assertOk();
    $this->assertModelMissing($announcement);
});
