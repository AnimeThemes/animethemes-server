<?php

declare(strict_types=1);

use App\Enums\Auth\ExtendedCrudPermission;
use App\Models\Auth\User;
use App\Models\Document\Page;
use Laravel\Sanctum\Sanctum;

test('protected', function () {
    $page = Page::factory()->trashed()->createOne();

    $response = $this->patch(route('api.page.restore', ['page' => $page]));

    $response->assertUnauthorized();
});

test('forbidden', function () {
    $page = Page::factory()->trashed()->createOne();

    $user = User::factory()->createOne();

    Sanctum::actingAs($user);

    $response = $this->patch(route('api.page.restore', ['page' => $page]));

    $response->assertForbidden();
});

test('trashed', function () {
    $page = Page::factory()->createOne();

    $user = User::factory()->withPermissions(ExtendedCrudPermission::RESTORE->format(Page::class))->createOne();

    Sanctum::actingAs($user);

    $response = $this->patch(route('api.page.restore', ['page' => $page]));

    $response->assertForbidden();
});

test('restored', function () {
    $page = Page::factory()->trashed()->createOne();

    $user = User::factory()->withPermissions(ExtendedCrudPermission::RESTORE->format(Page::class))->createOne();

    Sanctum::actingAs($user);

    $response = $this->patch(route('api.page.restore', ['page' => $page]));

    $response->assertOk();
    static::assertNotSoftDeleted($page);
});
