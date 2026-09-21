<?php

declare(strict_types=1);

use App\Enums\Models\Wiki\ResourceSite;
use App\Models\Wiki\Artist;
use App\Rules\Wiki\Resource\ArtistResourceLinkFormatRule;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

pest()->use(WithFaker::class);

test('fails for no pattern', function (): void {
    $attribute = fake()->word();

    $validator = Validator::make(
        [$attribute => fake()->url()],
        [$attribute => new ArtistResourceLinkFormatRule(ResourceSite::DISNEY_PLUS)],
    );

    expect($validator->passes())->toBeFalse();
});

test('passes for pattern', function (): void {
    /** @var ResourceSite $site */
    $site = Arr::random(ResourceSite::getForModel(Artist::class));

    $url = $site->formatResourceLink(Artist::class, fake()->randomDigitNotNull(), fake()->word(), 'null');

    $attribute = fake()->word();

    $validator = Validator::make(
        [$attribute => $url],
        [$attribute => new ArtistResourceLinkFormatRule($site)],
    );

    expect($validator->passes())->toBeTrue();
});

test('fails for trailing slash', function (): void {
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

    expect($site->getPattern(Artist::class) && $validator->passes())->toBeFalse();
});

test('fails for trailing slug', function (): void {
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

    expect($site->getPattern(Artist::class) && $validator->passes())->toBeFalse();
});

test('fails for other resources', function (): void {
    /** @var ResourceSite $site */
    $site = Arr::random(
        array_filter(
            ResourceSite::cases(),
            fn (ResourceSite $value): bool => ! in_array($value, ResourceSite::getForModel(Artist::class))
        )
    );

    $url = $site->formatResourceLink(Artist::class, fake()->randomDigitNotNull(), fake()->word());

    $attribute = fake()->word();

    $validator = Validator::make(
        [$attribute => $url],
        [$attribute => new ArtistResourceLinkFormatRule($site)],
    );

    expect($validator->passes())->toBeFalse();
});
