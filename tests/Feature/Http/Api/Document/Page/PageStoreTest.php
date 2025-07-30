<?php

declare(strict_types=1);

use App\Enums\Auth\CrudPermission;
use App\Models\Auth\User;
use App\Models\Document\Page;
use Laravel\Sanctum\Sanctum;

use function Pest\Laravel\post;

test('protected', function () {
    $page = Page::factory()->makeOne();

    $response = post(route('api.page.store', $page->toArray()));

    $response->assertUnauthorized();
});

test('forbidden', function () {
    $page = Page::factory()->makeOne();

    $user = User::factory()->createOne();

    Sanctum::actingAs($user);

    $response = post(route('api.page.store', $page->toArray()));

    $response->assertForbidden();
});

test('required fields', function () {
    $user = User::factory()->withPermissions(CrudPermission::CREATE->format(Page::class))->createOne();

    Sanctum::actingAs($user);

    $response = post(route('api.page.store'));

    $response->assertJsonValidationErrors([
        Page::ATTRIBUTE_BODY,
        Page::ATTRIBUTE_NAME,
        Page::ATTRIBUTE_SLUG,
    ]);
});

test('create', function () {
    $parameters = Page::factory()->raw();

    $user = User::factory()->withPermissions(CrudPermission::CREATE->format(Page::class))->createOne();

    Sanctum::actingAs($user);

    $response = post(route('api.page.store', $parameters));

    $response->assertCreated();
    $this->assertDatabaseCount(Page::class, 1);
});
