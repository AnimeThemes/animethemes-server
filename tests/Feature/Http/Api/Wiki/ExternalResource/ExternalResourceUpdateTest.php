<?php

declare(strict_types=1);

use App\Enums\Auth\CrudPermission;
use App\Enums\Models\Wiki\ResourceSite;
use App\Models\Auth\User;
use App\Models\Wiki\ExternalResource;
use Laravel\Sanctum\Sanctum;

use function Pest\Laravel\put;

test('protected', function () {
    $resource = ExternalResource::factory()->createOne();

    $parameters = array_merge(
        ExternalResource::factory()->raw(),
        [ExternalResource::ATTRIBUTE_SITE => ResourceSite::OFFICIAL_SITE->localize()]
    );

    $response = put(route('api.resource.update', ['resource' => $resource] + $parameters));

    $response->assertUnauthorized();
});

test('forbidden', function () {
    $resource = ExternalResource::factory()->createOne();

    $parameters = array_merge(
        ExternalResource::factory()->raw(),
        [ExternalResource::ATTRIBUTE_SITE => ResourceSite::OFFICIAL_SITE->localize()]
    );

    $user = User::factory()->createOne();

    Sanctum::actingAs($user);

    $response = put(route('api.resource.update', ['resource' => $resource] + $parameters));

    $response->assertForbidden();
});

test('trashed', function () {
    $resource = ExternalResource::factory()
        ->trashed()
        ->createOne([
            ExternalResource::ATTRIBUTE_SITE => ResourceSite::OFFICIAL_SITE,
        ]);

    $parameters = array_merge(
        ExternalResource::factory()->raw(),
        [ExternalResource::ATTRIBUTE_SITE => ResourceSite::OFFICIAL_SITE->localize()]
    );

    $user = User::factory()->withPermissions(CrudPermission::UPDATE->format(ExternalResource::class))->createOne();

    Sanctum::actingAs($user);

    $response = put(route('api.resource.update', ['resource' => $resource] + $parameters));

    $response->assertForbidden();
});

test('update', function () {
    $resource = ExternalResource::factory()->createOne([
        ExternalResource::ATTRIBUTE_SITE => ResourceSite::OFFICIAL_SITE,
    ]);

    $parameters = array_merge(
        ExternalResource::factory()->raw(),
        [ExternalResource::ATTRIBUTE_SITE => ResourceSite::OFFICIAL_SITE->localize()]
    );

    $user = User::factory()->withPermissions(CrudPermission::UPDATE->format(ExternalResource::class))->createOne();

    Sanctum::actingAs($user);

    $response = put(route('api.resource.update', ['resource' => $resource] + $parameters));

    $response->assertOk();
});
