<?php

declare(strict_types=1);

use App\Enums\Auth\CrudPermission;
use App\Enums\Models\Wiki\AnimeMediaFormat;
use App\Enums\Models\Wiki\AnimeSeason;
use App\Models\Auth\User;
use App\Models\Wiki\Anime;
use Illuminate\Support\Arr;
use Laravel\Sanctum\Sanctum;

test('protected', function () {
    $anime = Anime::factory()->createOne();

    $season = Arr::random(AnimeSeason::cases());
    $mediaFormat = Arr::random(AnimeMediaFormat::cases());

    $parameters = array_merge(
        Anime::factory()->raw(),
        [Anime::ATTRIBUTE_SEASON => $season->localize(), Anime::ATTRIBUTE_MEDIA_FORMAT => $mediaFormat->localize()],
    );

    $response = $this->put(route('api.anime.update', ['anime' => $anime] + $parameters));

    $response->assertUnauthorized();
});

test('forbidden', function () {
    $anime = Anime::factory()->createOne();

    $season = Arr::random(AnimeSeason::cases());
    $mediaFormat = Arr::random(AnimeMediaFormat::cases());

    $parameters = array_merge(
        Anime::factory()->raw(),
        [Anime::ATTRIBUTE_SEASON => $season->localize(), Anime::ATTRIBUTE_MEDIA_FORMAT => $mediaFormat->localize()],
    );

    $user = User::factory()->createOne();

    Sanctum::actingAs($user);

    $response = $this->put(route('api.anime.update', ['anime' => $anime] + $parameters));

    $response->assertForbidden();
});

test('trashed', function () {
    $anime = Anime::factory()->trashed()->createOne();

    $season = Arr::random(AnimeSeason::cases());
    $mediaFormat = Arr::random(AnimeMediaFormat::cases());

    $parameters = array_merge(
        Anime::factory()->raw(),
        [Anime::ATTRIBUTE_SEASON => $season->localize(), Anime::ATTRIBUTE_MEDIA_FORMAT => $mediaFormat->localize()],
    );

    $user = User::factory()->withPermissions(CrudPermission::UPDATE->format(Anime::class))->createOne();

    Sanctum::actingAs($user);

    $response = $this->put(route('api.anime.update', ['anime' => $anime] + $parameters));

    $response->assertForbidden();
});

test('update', function () {
    $anime = Anime::factory()->createOne();

    $season = Arr::random(AnimeSeason::cases());
    $mediaFormat = Arr::random(AnimeMediaFormat::cases());

    $parameters = array_merge(
        Anime::factory()->raw(),
        [Anime::ATTRIBUTE_SEASON => $season->localize(), Anime::ATTRIBUTE_MEDIA_FORMAT => $mediaFormat->localize()],
    );

    $user = User::factory()->withPermissions(CrudPermission::UPDATE->format(Anime::class))->createOne();

    Sanctum::actingAs($user);

    $response = $this->put(route('api.anime.update', ['anime' => $anime] + $parameters));

    $response->assertOk();
});
