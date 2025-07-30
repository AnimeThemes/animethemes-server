<?php

declare(strict_types=1);

use App\Enums\Auth\CrudPermission;
use App\Models\Admin\Feature;
use App\Models\Auth\User;
use Laravel\Sanctum\Sanctum;

test('protected', function () {
    $feature = Feature::factory()->createOne();

    $parameters = [
        Feature::ATTRIBUTE_VALUE => ! $feature->value,
    ];

    $response = $this->put(route('api.feature.update', ['feature' => $feature] + $parameters));

    $response->assertUnauthorized();
});

test('forbidden', function () {
    $feature = Feature::factory()->createOne();

    $parameters = [
        Feature::ATTRIBUTE_VALUE => ! $feature->value,
    ];

    $user = User::factory()->createOne();

    Sanctum::actingAs($user);

    $response = $this->put(route('api.feature.update', ['feature' => $feature] + $parameters));

    $response->assertForbidden();
});

test('update', function () {
    $feature = Feature::factory()->createOne();

    $parameters = [
        Feature::ATTRIBUTE_VALUE => ! $feature->value,
    ];

    $user = User::factory()->withPermissions(CrudPermission::UPDATE->format(Feature::class))->createOne();

    Sanctum::actingAs($user);

    $response = $this->put(route('api.feature.update', ['feature' => $feature] + $parameters));

    $response->assertOk();
});
