<?php

declare(strict_types=1);

use App\Enums\Auth\CrudPermission;
use App\Enums\Models\Wiki\ThemeType;
use App\Models\Auth\User;
use App\Models\Wiki\Anime;
use App\Models\Wiki\Anime\AnimeTheme;
use Illuminate\Support\Arr;
use Laravel\Sanctum\Sanctum;

test('protected', function () {
    $theme = AnimeTheme::factory()->for(Anime::factory())->createOne();

    $type = Arr::random(ThemeType::cases());

    $parameters = array_merge(
        AnimeTheme::factory()->raw(),
        [AnimeTheme::ATTRIBUTE_TYPE => $type->localize()],
    );

    $response = $this->put(route('api.animetheme.update', ['animetheme' => $theme] + $parameters));

    $response->assertUnauthorized();
});

test('forbidden', function () {
    $theme = AnimeTheme::factory()->for(Anime::factory())->createOne();

    $type = Arr::random(ThemeType::cases());

    $parameters = array_merge(
        AnimeTheme::factory()->raw(),
        [AnimeTheme::ATTRIBUTE_TYPE => $type->localize()],
    );

    $user = User::factory()->createOne();

    Sanctum::actingAs($user);

    $response = $this->put(route('api.animetheme.update', ['animetheme' => $theme] + $parameters));

    $response->assertForbidden();
});

test('trashed', function () {
    $theme = AnimeTheme::factory()
        ->trashed()
        ->for(Anime::factory())
        ->createOne();

    $type = Arr::random(ThemeType::cases());

    $parameters = array_merge(
        AnimeTheme::factory()->raw(),
        [AnimeTheme::ATTRIBUTE_TYPE => $type->localize()],
    );

    $user = User::factory()->withPermissions(CrudPermission::UPDATE->format(AnimeTheme::class))->createOne();

    Sanctum::actingAs($user);

    $response = $this->put(route('api.animetheme.update', ['animetheme' => $theme] + $parameters));

    $response->assertForbidden();
});

test('update', function () {
    $theme = AnimeTheme::factory()->for(Anime::factory())->createOne();

    $type = Arr::random(ThemeType::cases());

    $parameters = array_merge(
        AnimeTheme::factory()->raw(),
        [AnimeTheme::ATTRIBUTE_TYPE => $type->localize()],
    );

    $user = User::factory()->withPermissions(CrudPermission::UPDATE->format(AnimeTheme::class))->createOne();

    Sanctum::actingAs($user);

    $response = $this->put(route('api.animetheme.update', ['animetheme' => $theme] + $parameters));

    $response->assertOk();
});
