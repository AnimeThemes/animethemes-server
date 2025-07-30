<?php

declare(strict_types=1);

use App\Enums\Auth\CrudPermission;
use App\Enums\Models\Wiki\ImageFacet;
use App\Models\Auth\User;
use App\Models\Wiki\Image;
use Illuminate\Support\Arr;
use Laravel\Sanctum\Sanctum;

test('protected', function () {
    $image = Image::factory()->createOne();

    $facet = Arr::random(ImageFacet::cases());

    $parameters = array_merge(
        Image::factory()->raw(),
        [Image::ATTRIBUTE_FACET => $facet->localize()],
    );

    $response = $this->put(route('api.image.update', ['image' => $image] + $parameters));

    $response->assertUnauthorized();
});

test('forbidden', function () {
    $image = Image::factory()->createOne();

    $facet = Arr::random(ImageFacet::cases());

    $parameters = array_merge(
        Image::factory()->raw(),
        [Image::ATTRIBUTE_FACET => $facet->localize()],
    );

    $user = User::factory()->createOne();

    Sanctum::actingAs($user);

    $response = $this->put(route('api.image.update', ['image' => $image] + $parameters));

    $response->assertForbidden();
});

test('trashed', function () {
    $image = Image::factory()->trashed()->createOne();

    $facet = Arr::random(ImageFacet::cases());

    $parameters = array_merge(
        Image::factory()->raw(),
        [Image::ATTRIBUTE_FACET => $facet->localize()],
    );

    $user = User::factory()->withPermissions(CrudPermission::UPDATE->format(Image::class))->createOne();

    Sanctum::actingAs($user);

    $response = $this->put(route('api.image.update', ['image' => $image] + $parameters));

    $response->assertForbidden();
});

test('update', function () {
    $image = Image::factory()->createOne();

    $facet = Arr::random(ImageFacet::cases());

    $parameters = array_merge(
        Image::factory()->raw(),
        [Image::ATTRIBUTE_FACET => $facet->localize()],
    );

    $user = User::factory()->withPermissions(CrudPermission::UPDATE->format(Image::class))->createOne();

    Sanctum::actingAs($user);

    $response = $this->put(route('api.image.update', ['image' => $image] + $parameters));

    $response->assertOk();
});
