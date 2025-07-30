<?php

declare(strict_types=1);

use App\Enums\Auth\CrudPermission;
use App\Models\Auth\User;
use App\Models\Document\Page;
use Laravel\Sanctum\Sanctum;

test('protected', function () {
    $page = Page::factory()->createOne();

    $parameters = Page::factory()->raw();

    $response = $this->put(route('api.page.update', ['page' => $page] + $parameters));

    $response->assertUnauthorized();
});

test('forbidden', function () {
    $page = Page::factory()->createOne();

    $parameters = Page::factory()->raw();

    $user = User::factory()->createOne();

    Sanctum::actingAs($user);

    $response = $this->put(route('api.page.update', ['page' => $page] + $parameters));

    $response->assertForbidden();
});

test('trashed', function () {
    $page = Page::factory()->trashed()->createOne();

    $parameters = Page::factory()->raw();

    $user = User::factory()->withPermissions(CrudPermission::UPDATE->format(Page::class))->createOne();

    Sanctum::actingAs($user);

    $response = $this->put(route('api.page.update', ['page' => $page] + $parameters));

    $response->assertForbidden();
});

test('update', function () {
    $page = Page::factory()->createOne();

    $parameters = Page::factory()->raw();

    $user = User::factory()->withPermissions(CrudPermission::UPDATE->format(Page::class))->createOne();

    Sanctum::actingAs($user);

    $response = $this->put(route('api.page.update', ['page' => $page] + $parameters));

    $response->assertOk();
});
