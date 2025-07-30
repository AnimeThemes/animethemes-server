<?php

declare(strict_types=1);

use App\Enums\Auth\ExtendedCrudPermission;
use App\Models\Auth\User;
use App\Models\Document\Page;
use Laravel\Sanctum\Sanctum;

use function Pest\Laravel\delete;

test('protected', function () {
    $page = Page::factory()->createOne();

    $response = delete(route('api.page.forceDelete', ['page' => $page]));

    $response->assertUnauthorized();
});

test('forbidden', function () {
    $page = Page::factory()->createOne();

    $user = User::factory()->createOne();

    Sanctum::actingAs($user);

    $response = delete(route('api.page.forceDelete', ['page' => $page]));

    $response->assertForbidden();
});

test('deleted', function () {
    $page = Page::factory()->createOne();

    $user = User::factory()->withPermissions(ExtendedCrudPermission::FORCE_DELETE->format(Page::class))->createOne();

    Sanctum::actingAs($user);

    $response = delete(route('api.page.forceDelete', ['page' => $page]));

    $response->assertOk();
    $this->assertModelMissing($page);
});
