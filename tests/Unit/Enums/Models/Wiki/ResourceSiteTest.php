<?php

declare(strict_types=1);

use App\Enums\Models\Wiki\ResourceSite;
use App\Models\Wiki\Anime;
use App\Models\Wiki\Studio;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

pest()->use(WithFaker::class);

test('parse id from anime resource', function (): void {
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

    expect(ResourceSite::parseIdFromLink($link))->toEqual(strval($animeId));
});

test('parse id from studio resource', function (): void {
    $studioId = fake()->randomDigitNotNull();

    /** @var ResourceSite $site */
    $site = Arr::random([
        ResourceSite::ANIDB,
        ResourceSite::ANILIST,
        ResourceSite::ANN,
        ResourceSite::MAL,
    ]);

    $link = $site->formatResourceLink(Studio::class, $studioId);

    expect(ResourceSite::parseIdFromLink($link))->toEqual(strval($studioId));
});

test('fail parse anime planet id from studio resource', function (): void {
    $link = ResourceSite::ANIME_PLANET->formatResourceLink(
        Studio::class,
        fake()->randomDigitNotNull(),
        fake()->slug()
    );

    expect(ResourceSite::parseIdFromLink($link))->toBeEmpty();
    Http::assertNothingSent();
});

test('fail parse anime planet id from anime resource', function (): void {
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

    expect(ResourceSite::parseIdFromLink($link))->toBeEmpty();
    Http::assertSentCount(1);
});

test('parse anime planet id from anime resource', function (): void {
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

    expect(ResourceSite::parseIdFromLink($link))->toEqual(strval($id));
    Http::assertSentCount(1);
});

test('parse kitsu id for id from anime resource', function (): void {
    $id = fake()->randomDigitNotNull();

    $link = ResourceSite::KITSU->formatResourceLink(Anime::class, $id);

    expect(ResourceSite::parseIdFromLink($link))->toEqual($id);
});

test('parse kitsu id for slug from anime resource', function (): void {
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

    expect(ResourceSite::parseIdFromLink($linkWithSlug))->toEqual(strval($id));
    Http::assertSentCount(1);
});
