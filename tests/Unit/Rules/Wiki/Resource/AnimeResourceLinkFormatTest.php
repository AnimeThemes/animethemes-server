<?php

declare(strict_types=1);

use App\Enums\Models\Wiki\ResourceSite;
use App\Models\Wiki\Anime;
use App\Rules\Wiki\Resource\AnimeResourceLinkFormatRule;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

uses(Illuminate\Foundation\Testing\WithFaker::class);

test('fails for no pattern', function () {
    $attribute = fake()->word();

    $validator = Validator::make(
        [$attribute => fake()->url()],
        [$attribute => new AnimeResourceLinkFormatRule(ResourceSite::YOUTUBE_MUSIC)],
    );

    static::assertFalse($validator->passes());
});

test('passes for pattern', function () {
    /** @var ResourceSite $site */
    $site = Arr::random(ResourceSite::getForModel(Anime::class));

    $url = $site->formatResourceLink(Anime::class, fake()->randomDigitNotNull(), fake()->word(), 'null');

    $attribute = fake()->word();

    $validator = Validator::make(
        [$attribute => $url],
        [$attribute => new AnimeResourceLinkFormatRule($site)],
    );

    static::assertTrue($validator->passes());
});

test('fails for trailing slash', function () {
    /** @var ResourceSite $site */
    $site = Arr::random(ResourceSite::getForModel(Anime::class));

    $url = $site->formatResourceLink(Anime::class, fake()->randomDigitNotNull(), fake()->word());

    $url = Str::of($url)
        ->append('/')
        ->__toString();

    $attribute = fake()->word();

    $validator = Validator::make(
        [$attribute => $url],
        [$attribute => new AnimeResourceLinkFormatRule($site)],
    );

    static::assertFalse($site->getPattern(Anime::class) && $validator->passes());
});

test('fails for trailing slug', function () {
    /** @var ResourceSite $site */
    $site = Arr::random(ResourceSite::getForModel(Anime::class));

    $url = $site->formatResourceLink(Anime::class, fake()->randomDigitNotNull(), fake()->word());

    $url = Str::of($url)
        ->append('/')
        ->append(fake()->word())
        ->__toString();

    $attribute = fake()->word();

    $validator = Validator::make(
        [$attribute => $url],
        [$attribute => new AnimeResourceLinkFormatRule($site)],
    );

    static::assertFalse($site->getPattern(Anime::class) && $validator->passes());
});

test('fails for other resources', function () {
    /** @var ResourceSite $site */
    $site = Arr::random(
        array_filter(
            ResourceSite::cases(),
            fn ($value) => ! in_array($value, ResourceSite::getForModel(Anime::class))
        )
    );

    $url = $site->formatResourceLink(Anime::class, fake()->randomDigitNotNull(), fake()->word());

    $attribute = fake()->word();

    $validator = Validator::make(
        [$attribute => $url],
        [$attribute => new AnimeResourceLinkFormatRule($site)],
    );

    static::assertFalse($validator->passes());
});
