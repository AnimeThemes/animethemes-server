<?php

declare(strict_types=1);

use App\Enums\Auth\CrudPermission;
use App\Models\Auth\User;
use App\Models\Document\Page;
use Laravel\Sanctum\Sanctum;

test('protected', function () {
    $page = Page::factory()->createOne();

    $response = $this->delete(route('api.page.destroy', ['page' => $page]));

    $response->assertUnauthorized();
});

test('forbidden', function () {
    $page = Page::factory()->createOne();

    $user = User::factory()->createOne();

    Sanctum::actingAs($user);

    $response = $this->delete(route('api.page.destroy', ['page' => $page]));

    $response->assertForbidden();
});

test('trashed', function () {
    $page = Page::factory()->trashed()->createOne();

    $user = User::factory()->withPermissions(CrudPermission::DELETE->format(Page::class))->createOne();

    Sanctum::actingAs($user);

    $response = $this->delete(route('api.page.destroy', ['page' => $page]));

    $response->assertNotFound();
});

test('deleted', function () {
    $page = Page::factory()->createOne();

    $user = User::factory()->withPermissions(CrudPermission::DELETE->format(Page::class))->createOne();

    Sanctum::actingAs($user);

    $response = $this->delete(route('api.page.destroy', ['page' => $page]));

    $response->assertOk();
    static::assertSoftDeleted($page);
});
