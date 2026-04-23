<?php

declare(strict_types=1);

use App\Enums\Models\Wiki\ResourceSite;
use App\Models\Wiki\Anime;
use App\Rules\Wiki\Resource\AnimeResourceLinkFormatRule;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

uses(WithFaker::class);

test('fails for no pattern', function (): void {
    $attribute = fake()->word();

    $validator = Validator::make(
        [$attribute => fake()->url()],
        [$attribute => new AnimeResourceLinkFormatRule(ResourceSite::YOUTUBE_MUSIC)],
    );

    $this->assertFalse($validator->passes());
});

test('passes for pattern', function (): void {
    /** @var ResourceSite $site */
    $site = Arr::random(ResourceSite::getForModel(Anime::class));

    $url = $site->formatResourceLink(Anime::class, fake()->randomDigitNotNull(), fake()->word(), 'null');

    $attribute = fake()->word();

    $validator = Validator::make(
        [$attribute => $url],
        [$attribute => new AnimeResourceLinkFormatRule($site)],
    );

    $this->assertTrue($validator->passes());
});

test('fails for trailing slash', function (): void {
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

    $this->assertFalse($site->getPattern(Anime::class) && $validator->passes());
});

test('fails for trailing slug', function (): void {
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

    $this->assertFalse($site->getPattern(Anime::class) && $validator->passes());
});

test('fails for other resources', function (): void {
    /** @var ResourceSite $site */
    $site = Arr::random(
        array_filter(
            ResourceSite::cases(),
            fn (ResourceSite $value): bool => ! in_array($value, ResourceSite::getForModel(Anime::class))
        )
    );

    $url = $site->formatResourceLink(Anime::class, fake()->randomDigitNotNull(), fake()->word());

    $attribute = fake()->word();

    $validator = Validator::make(
        [$attribute => $url],
        [$attribute => new AnimeResourceLinkFormatRule($site)],
    );

    $this->assertFalse($validator->passes());
});
