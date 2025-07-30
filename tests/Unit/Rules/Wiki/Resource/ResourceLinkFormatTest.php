<?php

declare(strict_types=1);

use App\Enums\Models\Wiki\ResourceSite;
use App\Models\Wiki\Anime;
use App\Models\Wiki\Artist;
use App\Models\Wiki\Song;
use App\Models\Wiki\Studio;
use App\Rules\Wiki\Resource\ResourceLinkFormatRule;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

uses(Illuminate\Foundation\Testing\WithFaker::class);

test('passes for no site', function () {
    $attribute = fake()->word();

    $validator = Validator::make(
        [$attribute => fake()->url()],
        [$attribute => new ResourceLinkFormatRule()],
    );

    static::assertTrue($validator->passes());
});

test('passes for no pattern', function () {
    $attribute = fake()->word();

    $validator = Validator::make(
        [$attribute => fake()->url()],
        [$attribute => new ResourceLinkFormatRule(ResourceSite::OFFICIAL_SITE)],
    );

    static::assertTrue($validator->passes());
});

test('passes for anime resource', function () {
    /** @var ResourceSite $site */
    $site = Arr::random([
        ResourceSite::X,
        ResourceSite::ANIDB,
        ResourceSite::ANILIST,
        ResourceSite::ANIME_PLANET,
        ResourceSite::ANN,
        ResourceSite::KITSU,
        ResourceSite::MAL,
        ResourceSite::YOUTUBE,
    ]);

    $url = $site->formatResourceLink(Anime::class, fake()->randomDigitNotNull(), fake()->word());

    $attribute = fake()->word();

    $validator = Validator::make(
        [$attribute => $url],
        [$attribute => new ResourceLinkFormatRule($site)],
    );

    static::assertTrue($validator->passes());
});

test('passes for artist resource', function () {
    /** @var ResourceSite $site */
    $site = Arr::random([
        ResourceSite::X,
        ResourceSite::ANIDB,
        ResourceSite::ANILIST,
        ResourceSite::ANIME_PLANET,
        ResourceSite::ANN,
        ResourceSite::MAL,
        ResourceSite::SPOTIFY,
        ResourceSite::YOUTUBE,
    ]);

    $url = $site->formatResourceLink(Artist::class, fake()->randomDigitNotNull(), fake()->word());

    $attribute = fake()->word();

    $validator = Validator::make(
        [$attribute => $url],
        [$attribute => new ResourceLinkFormatRule($site)],
    );

    static::assertTrue($validator->passes());
});

test('passes for song resource', function () {
    /** @var ResourceSite $site */
    $site = Arr::random([
        ResourceSite::SPOTIFY,
        ResourceSite::YOUTUBE_MUSIC,
        ResourceSite::YOUTUBE,
        ResourceSite::APPLE_MUSIC,
        ResourceSite::AMAZON_MUSIC,
    ]);

    $url = $site->formatResourceLink(Song::class, fake()->randomDigitNotNull(), fake()->word());

    $attribute = fake()->word();

    $validator = Validator::make(
        [$attribute => $url],
        [$attribute => new ResourceLinkFormatRule($site)],
    );

    static::assertTrue($validator->passes());
});

test('passes for studio resource', function () {
    /** @var ResourceSite $site */
    $site = Arr::random([
        ResourceSite::X,
        ResourceSite::ANIDB,
        ResourceSite::ANILIST,
        ResourceSite::ANIME_PLANET,
        ResourceSite::ANN,
        ResourceSite::MAL,
    ]);

    $url = $site->formatResourceLink(Studio::class, fake()->randomDigitNotNull(), fake()->word());

    $attribute = fake()->word();

    $validator = Validator::make(
        [$attribute => $url],
        [$attribute => new ResourceLinkFormatRule($site)],
    );

    static::assertTrue($validator->passes());
});

test('fails for trailing slash', function () {
    // Resource sites that can be attached for all models.
    $site = Arr::random([
        ResourceSite::ANIDB,
    ]);

    $url = $site->formatResourceLink(Anime::class, fake()->randomDigitNotNull(), fake()->word());

    $url = Str::of($url)
        ->append('/')
        ->__toString();

    $attribute = fake()->word();

    $validator = Validator::make(
        [$attribute => $url],
        [$attribute => new ResourceLinkFormatRule($site)],
    );

    static::assertFalse($validator->passes());
});
