<?php

declare(strict_types=1);

use App\Enums\Auth\CrudPermission;
use App\Models\Admin\FeaturedTheme;
use App\Models\Auth\User;
use Laravel\Sanctum\Sanctum;

test('protected', function () {
    $featuredTheme = FeaturedTheme::factory()->createOne();

    $response = $this->delete(route('api.featuredtheme.destroy', ['featuredtheme' => $featuredTheme]));

    $response->assertUnauthorized();
});

test('forbidden', function () {
    $featuredTheme = FeaturedTheme::factory()->createOne();

    $user = User::factory()->createOne();

    Sanctum::actingAs($user);

    $response = $this->delete(route('api.featuredtheme.destroy', ['featuredtheme' => $featuredTheme]));

    $response->assertForbidden();
});

test('deleted', function () {
    $featuredTheme = FeaturedTheme::factory()->createOne();

    $user = User::factory()->withPermissions(CrudPermission::DELETE->format(FeaturedTheme::class))->createOne();

    Sanctum::actingAs($user);

    $response = $this->delete(route('api.featuredtheme.destroy', ['featuredtheme' => $featuredTheme]));

    $response->assertOk();
    static::assertModelMissing($featuredTheme);
});
