<?php

declare(strict_types=1);

use App\Enums\Auth\CrudPermission;
use App\Enums\Models\Wiki\ThemeType;
use App\Models\Auth\User;
use App\Models\Wiki\Anime;
use App\Models\Wiki\Anime\AnimeTheme;
use Illuminate\Support\Arr;
use Laravel\Sanctum\Sanctum;

use function Pest\Laravel\put;

test('protected', function (): void {
    $theme = AnimeTheme::factory()->for(Anime::factory())->createOne();

    $type = Arr::random(ThemeType::cases());

    $parameters = array_merge(
        AnimeTheme::factory()->raw(),
        [AnimeTheme::ATTRIBUTE_TYPE => $type->localize()],
    );

    $response = put(route('api.animetheme.update', ['animetheme' => $theme] + $parameters));

    $response->assertUnauthorized();
});

test('forbidden', function (): void {
    $theme = AnimeTheme::factory()->for(Anime::factory())->createOne();

    $type = Arr::random(ThemeType::cases());

    $parameters = array_merge(
        AnimeTheme::factory()->raw(),
        [AnimeTheme::ATTRIBUTE_TYPE => $type->localize()],
    );

    $user = User::factory()->createOne();

    Sanctum::actingAs($user);

    $response = put(route('api.animetheme.update', ['animetheme' => $theme] + $parameters));

    $response->assertForbidden();
});

test('trashed', function (): void {
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

    $response = put(route('api.animetheme.update', ['animetheme' => $theme] + $parameters));

    $response->assertNotFound();
});

test('update', function (): void {
    $theme = AnimeTheme::factory()->for(Anime::factory())->createOne();

    $type = Arr::random(ThemeType::cases());

    $parameters = array_merge(
        AnimeTheme::factory()->raw(),
        [AnimeTheme::ATTRIBUTE_TYPE => $type->localize()],
    );

    $user = User::factory()->withPermissions(CrudPermission::UPDATE->format(AnimeTheme::class))->createOne();

    Sanctum::actingAs($user);

    $response = put(route('api.animetheme.update', ['animetheme' => $theme] + $parameters));

    $response->assertOk();
});
