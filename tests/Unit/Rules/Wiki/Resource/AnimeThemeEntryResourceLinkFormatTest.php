<?php

declare(strict_types=1);

use App\Enums\Models\Wiki\ResourceSite;
use App\Models\Wiki\Entry;
use App\Rules\Wiki\Resource\EntryResourceLinkFormatRule;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

pest()->use(WithFaker::class);

test('fails for no pattern', function (): void {
    $attribute = fake()->word();

    $validator = Validator::make(
        [$attribute => fake()->url()],
        [$attribute => new EntryResourceLinkFormatRule(ResourceSite::YOUTUBE_MUSIC)],
    );

    expect($validator->passes())->toBeFalse();
});

test('passes for pattern', function (): void {
    /** @var ResourceSite $site */
    $site = Arr::random(ResourceSite::getForModel(Entry::class));

    $url = $site->formatResourceLink(Entry::class, fake()->randomDigitNotNull(), fake()->word(), 'null');

    $attribute = fake()->word();

    $validator = Validator::make(
        [$attribute => $url],
        [$attribute => new EntryResourceLinkFormatRule($site)],
    );

    expect($validator->passes())->toBeTrue();
});

test('fails for trailing slash', function (): void {
    /** @var ResourceSite $site */
    $site = Arr::random(ResourceSite::getForModel(Entry::class));

    $url = $site->formatResourceLink(Entry::class, fake()->randomDigitNotNull(), fake()->word());

    $url = Str::of($url)
        ->append('/')
        ->__toString();

    $attribute = fake()->word();

    $validator = Validator::make(
        [$attribute => $url],
        [$attribute => new EntryResourceLinkFormatRule($site)],
    );

    expect($site->getPattern(Entry::class) && $validator->passes())->toBeFalse();
});

test('fails for trailing slug', function (): void {
    /** @var ResourceSite $site */
    $site = Arr::random(ResourceSite::getForModel(Entry::class));

    $url = $site->formatResourceLink(Entry::class, fake()->randomDigitNotNull(), fake()->word());

    $url = Str::of($url)
        ->append('/')
        ->append(fake()->word())
        ->__toString();

    $attribute = fake()->word();

    $validator = Validator::make(
        [$attribute => $url],
        [$attribute => new EntryResourceLinkFormatRule($site)],
    );

    expect($site->getPattern(Entry::class) && $validator->passes())->toBeFalse();
});

test('fails for other resources', function (): void {
    /** @var ResourceSite $site */
    $site = Arr::random(
        array_filter(
            ResourceSite::cases(),
            fn (ResourceSite $value): bool => ! in_array($value, ResourceSite::getForModel(Entry::class))
        )
    );

    $url = $site->formatResourceLink(Entry::class, fake()->randomDigitNotNull(), fake()->word());

    $attribute = fake()->word();

    $validator = Validator::make(
        [$attribute => $url],
        [$attribute => new EntryResourceLinkFormatRule($site)],
    );

    expect($validator->passes())->toBeFalse();
});
