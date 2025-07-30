<?php

declare(strict_types=1);

use App\Enums\Auth\CrudPermission;
use App\Enums\Models\Wiki\ThemeType;
use App\Models\Auth\User;
use App\Models\Wiki\Anime;
use App\Models\Wiki\Anime\AnimeTheme;
use Illuminate\Support\Arr;
use Laravel\Sanctum\Sanctum;

use function Pest\Laravel\post;

test('protected', function () {
    $theme = AnimeTheme::factory()->for(Anime::factory())->makeOne();

    $response = post(route('api.animetheme.store', $theme->toArray()));

    $response->assertUnauthorized();
});

test('forbidden', function () {
    $theme = AnimeTheme::factory()->for(Anime::factory())->makeOne();

    $user = User::factory()->createOne();

    Sanctum::actingAs($user);

    $response = post(route('api.animetheme.store', $theme->toArray()));

    $response->assertForbidden();
});

test('required fields', function () {
    $user = User::factory()->withPermissions(CrudPermission::CREATE->format(AnimeTheme::class))->createOne();

    Sanctum::actingAs($user);

    $response = post(route('api.animetheme.store'));

    $response->assertJsonValidationErrors([
        AnimeTheme::ATTRIBUTE_ANIME,
        AnimeTheme::ATTRIBUTE_SLUG,
        AnimeTheme::ATTRIBUTE_TYPE,
    ]);
});

test('create', function () {
    $anime = Anime::factory()->createOne();

    $type = Arr::random(ThemeType::cases());

    $parameters = array_merge(
        AnimeTheme::factory()->raw(),
        [AnimeTheme::ATTRIBUTE_TYPE => $type->localize()],
        [AnimeTheme::ATTRIBUTE_ANIME => $anime->getKey()],
    );

    $user = User::factory()->withPermissions(CrudPermission::CREATE->format(AnimeTheme::class))->createOne();

    Sanctum::actingAs($user);

    $response = post(route('api.animetheme.store', $parameters));

    $response->assertCreated();
    $this->assertDatabaseCount(AnimeTheme::class, 1);
});
