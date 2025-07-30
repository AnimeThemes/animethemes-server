<?php

declare(strict_types=1);

use App\Enums\Models\Wiki\ResourceSite;
use App\Models\Wiki\Artist;
use App\Rules\Wiki\Resource\ArtistResourceLinkFormatRule;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

uses(Illuminate\Foundation\Testing\WithFaker::class);

test('fails for no pattern', function () {
    $attribute = fake()->word();

    $validator = Validator::make(
        [$attribute => fake()->url()],
        [$attribute => new ArtistResourceLinkFormatRule(ResourceSite::DISNEY_PLUS)],
    );

    $this->assertFalse($validator->passes());
});

test('passes for pattern', function () {
    /** @var ResourceSite $site */
    $site = Arr::random(ResourceSite::getForModel(Artist::class));

    $url = $site->formatResourceLink(Artist::class, fake()->randomDigitNotNull(), fake()->word(), 'null');

    $attribute = fake()->word();

    $validator = Validator::make(
        [$attribute => $url],
        [$attribute => new ArtistResourceLinkFormatRule($site)],
    );

    $this->assertTrue($validator->passes());
});

test('fails for trailing slash', function () {
    /** @var ResourceSite $site */
    $site = Arr::random(ResourceSite::getForModel(Artist::class));

    $url = $site->formatResourceLink(Artist::class, fake()->randomDigitNotNull(), fake()->word());

    $url = Str::of($url)
        ->append('/')
        ->__toString();

    $attribute = fake()->word();

    $validator = Validator::make(
        [$attribute => $url],
        [$attribute => new ArtistResourceLinkFormatRule($site)],
    );

    $this->assertFalse($site->getPattern(Artist::class) && $validator->passes());
});

test('fails for trailing slug', function () {
    /** @var ResourceSite $site */
    $site = Arr::random(ResourceSite::getForModel(Artist::class));

    $url = $site->formatResourceLink(Artist::class, fake()->randomDigitNotNull(), fake()->word());

    $url = Str::of($url)
        ->append('/')
        ->append(fake()->word())
        ->__toString();

    $attribute = fake()->word();

    $validator = Validator::make(
        [$attribute => $url],
        [$attribute => new ArtistResourceLinkFormatRule($site)],
    );

    $this->assertFalse($site->getPattern(Artist::class) && $validator->passes());
});

test('fails for other resources', function () {
    /** @var ResourceSite $site */
    $site = Arr::random(
        array_filter(
            ResourceSite::cases(),
            fn ($value) => ! in_array($value, ResourceSite::getForModel(Artist::class))
        )
    );

    $url = $site->formatResourceLink(Artist::class, fake()->randomDigitNotNull(), fake()->word());

    $attribute = fake()->word();

    $validator = Validator::make(
        [$attribute => $url],
        [$attribute => new ArtistResourceLinkFormatRule($site)],
    );

    $this->assertFalse($validator->passes());
});
