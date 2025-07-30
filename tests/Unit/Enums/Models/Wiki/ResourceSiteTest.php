<?php

declare(strict_types=1);

use App\Enums\Models\Wiki\ResourceSite;
use App\Models\Wiki\Anime;
use App\Models\Wiki\Studio;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

uses(Illuminate\Foundation\Testing\WithFaker::class);

test('parse id from anime resource', function () {
    $animeId = fake()->randomDigitNotNull();

    /** @var ResourceSite $site */
    $site = Arr::random([
        ResourceSite::ANIDB,
        ResourceSite::ANILIST,
        ResourceSite::ANN,
        ResourceSite::MAL,
        ResourceSite::NETFLIX,
    ]);

    $link = $site->formatResourceLink(Anime::class, $animeId);

    $this->assertEquals(strval($animeId), ResourceSite::parseIdFromLink($link));
});

test('parse id from studio resource', function () {
    $studioId = fake()->randomDigitNotNull();

    /** @var ResourceSite $site */
    $site = Arr::random([
        ResourceSite::ANIDB,
        ResourceSite::ANILIST,
        ResourceSite::ANN,
        ResourceSite::MAL,
    ]);

    $link = $site->formatResourceLink(Studio::class, $studioId);

    $this->assertEquals(strval($studioId), ResourceSite::parseIdFromLink($link));
});

test('fail parse anime planet id from studio resource', function () {
    $link = ResourceSite::ANIME_PLANET->formatResourceLink(
        Studio::class,
        fake()->randomDigitNotNull(),
        fake()->slug()
    );

    $this->assertEmpty(ResourceSite::parseIdFromLink($link));
    Http::assertNothingSent();
});

test('fail parse anime planet id from anime resource', function () {
    Http::fake([
        'https://www.anime-planet.com/anime/*' => Http::response([
            fake()->word() => fake()->word(),
        ]),
    ]);

    $link = ResourceSite::ANIME_PLANET->formatResourceLink(
        Anime::class,
        fake()->randomDigitNotNull(),
        fake()->slug()
    );

    $this->assertEmpty(ResourceSite::parseIdFromLink($link));
    Http::assertSentCount(1);
});

test('parse anime planet id from anime resource', function () {
    $id = fake()->randomDigitNotNull();

    Http::fake([
        'https://www.anime-planet.com/anime/*' => Http::response(
            "
                $(function() {
                    window.AP_VARS = $.extend(true, {}, window.AP_VARS, {
                        ENTRY_INFO: {
                            type: \"anime\",
                            id: \"$id\",
                            url: \"{fake()->word()}\"
                        }
                    }
                });
                "
        ),
    ]);

    $link = ResourceSite::ANIME_PLANET->formatResourceLink(Anime::class, $id, fake()->slug());

    $this->assertEquals(strval($id), ResourceSite::parseIdFromLink($link));
    Http::assertSentCount(1);
});

test('parse kitsu id for id from anime resource', function () {
    $id = fake()->randomDigitNotNull();

    $link = ResourceSite::KITSU->formatResourceLink(Anime::class, $id);

    $this->assertEquals($id, ResourceSite::parseIdFromLink($link));
});

test('parse kitsu id for slug from anime resource', function () {
    $id = fake()->randomDigitNotNull();
    $slug = fake()->slug();

    $linkWithSlug = Str::of(ResourceSite::KITSU->formatResourceLink(Anime::class, $id))
        ->replace(strval($id), $slug)
        ->__toString();

    Http::fake([
        'https://kitsu.io/api/graphql' => Http::response([
            'data' => [
                'findAnimeBySlug' => [
                    'id' => strval($id),
                ],
            ],
        ]),
    ]);

    $this->assertEquals(strval($id), ResourceSite::parseIdFromLink($linkWithSlug));
    Http::assertSentCount(1);
});
