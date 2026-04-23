<?php

declare(strict_types=1);

use App\Enums\Auth\ExtendedCrudPermission;
use App\Models\Auth\User;
use App\Models\Document\Page;
use Laravel\Sanctum\Sanctum;

use function Pest\Laravel\patch;

test('protected', function (): void {
    $page = Page::factory()->trashed()->createOne();

    $response = patch(route('api.page.restore', ['page' => $page]));

    $response->assertUnauthorized();
});

test('forbidden', function (): void {
    $page = Page::factory()->trashed()->createOne();

    $user = User::factory()->createOne();

    Sanctum::actingAs($user);

    $response = patch(route('api.page.restore', ['page' => $page]));

    $response->assertForbidden();
});

test('trashed', function (): void {
    $page = Page::factory()->createOne();

    $user = User::factory()->withPermissions(ExtendedCrudPermission::RESTORE->format(Page::class))->createOne();

    Sanctum::actingAs($user);

    $response = patch(route('api.page.restore', ['page' => $page]));

    $response->assertOk();
});

test('restored', function (): void {
    $page = Page::factory()->trashed()->createOne();

    $user = User::factory()->withPermissions(ExtendedCrudPermission::RESTORE->format(Page::class))->createOne();

    Sanctum::actingAs($user);

    $response = patch(route('api.page.restore', ['page' => $page]));

    $response->assertOk();
    $this->assertNotSoftDeleted($page);
});
