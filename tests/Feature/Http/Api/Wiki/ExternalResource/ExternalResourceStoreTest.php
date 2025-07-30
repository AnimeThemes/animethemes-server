<?php

declare(strict_types=1);

use App\Enums\Auth\CrudPermission;
use App\Enums\Models\Wiki\ResourceSite;
use App\Models\Auth\User;
use App\Models\Wiki\ExternalResource;
use Laravel\Sanctum\Sanctum;

use function Pest\Laravel\post;

test('protected', function () {
    $resource = ExternalResource::factory()->makeOne();

    $response = post(route('api.resource.store', $resource->toArray()));

    $response->assertUnauthorized();
});

test('forbidden', function () {
    $resource = ExternalResource::factory()->makeOne();

    $user = User::factory()->createOne();

    Sanctum::actingAs($user);

    $response = post(route('api.resource.store', $resource->toArray()));

    $response->assertForbidden();
});

test('required fields', function () {
    $user = User::factory()->withPermissions(CrudPermission::CREATE->format(ExternalResource::class))->createOne();

    Sanctum::actingAs($user);

    $response = post(route('api.resource.store'));

    $response->assertJsonValidationErrors([
        ExternalResource::ATTRIBUTE_LINK,
        ExternalResource::ATTRIBUTE_SITE,
    ]);
});

test('create', function () {
    $parameters = array_merge(
        ExternalResource::factory()->raw(),
        [ExternalResource::ATTRIBUTE_SITE => ResourceSite::OFFICIAL_SITE->localize()],
    );

    $user = User::factory()->withPermissions(CrudPermission::CREATE->format(ExternalResource::class))->createOne();

    Sanctum::actingAs($user);

    $response = post(route('api.resource.store', $parameters));

    $response->assertCreated();
    $this->assertDatabaseCount(ExternalResource::class, 1);
});
