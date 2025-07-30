<?php

declare(strict_types=1);

use App\Enums\Auth\CrudPermission;
use App\Enums\Models\Wiki\AnimeMediaFormat;
use App\Enums\Models\Wiki\AnimeSeason;
use App\Models\Auth\User;
use App\Models\Wiki\Anime;
use Illuminate\Support\Arr;
use Laravel\Sanctum\Sanctum;

use function Pest\Laravel\post;

test('protected', function () {
    $anime = Anime::factory()->makeOne();

    $response = post(route('api.anime.store', $anime->toArray()));

    $response->assertUnauthorized();
});

test('forbidden', function () {
    $anime = Anime::factory()->makeOne();

    $user = User::factory()->createOne();

    Sanctum::actingAs($user);

    $response = post(route('api.anime.store', $anime->toArray()));

    $response->assertForbidden();
});

test('required fields', function () {
    $user = User::factory()->withPermissions(CrudPermission::CREATE->format(Anime::class))->createOne();

    Sanctum::actingAs($user);

    $response = post(route('api.anime.store'));

    $response->assertJsonValidationErrors([
        Anime::ATTRIBUTE_NAME,
        Anime::ATTRIBUTE_SEASON,
        Anime::ATTRIBUTE_MEDIA_FORMAT,
        Anime::ATTRIBUTE_SLUG,
        Anime::ATTRIBUTE_YEAR,
    ]);
});

test('create', function () {
    $season = Arr::random(AnimeSeason::cases());
    $mediaFormat = Arr::random(AnimeMediaFormat::cases());

    $parameters = array_merge(
        Anime::factory()->raw(),
        [Anime::ATTRIBUTE_SEASON => $season->localize(), Anime::ATTRIBUTE_MEDIA_FORMAT => $mediaFormat->localize()],
    );

    $user = User::factory()->withPermissions(CrudPermission::CREATE->format(Anime::class))->createOne();

    Sanctum::actingAs($user);

    $response = post(route('api.anime.store', $parameters));

    $response->assertCreated();
    $this->assertDatabaseCount(Anime::class, 1);
});
