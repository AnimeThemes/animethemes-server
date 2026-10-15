<?php

declare(strict_types=1);

use App\Enums\Models\Wiki\ResourceSite;
use App\Models\Wiki\Anime;
use App\Models\Wiki\Studio;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Arr;

pest()->use(WithFaker::class);

test('parse id from anime resource', function (): void {
    $animeId = fake()->randomDigitNotNull();

    /** @var ResourceSite $site */
    $site = Arr::random([
        ResourceSite::ANIDB,
        ResourceSite::ANILIST,
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
        ResourceSite::MAL,
    ]);

    $link = $site->formatResourceLink(Studio::class, $studioId);

    expect(ResourceSite::parseIdFromLink($link))->toEqual(strval($studioId));
});
