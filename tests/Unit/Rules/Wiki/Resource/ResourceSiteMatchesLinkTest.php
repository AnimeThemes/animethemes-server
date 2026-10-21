<?php

declare(strict_types=1);

use App\Enums\Models\Wiki\ResourceSite;
use App\Rules\Wiki\Resource\ResourceSiteMatchesLinkRule;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Validator;

pest()->use(WithFaker::class);

test('passes if site matches link', function (): void {
    $site = null;
    $domain = null;

    while ($domain === null) {
        $site = Arr::random(ResourceSite::cases());

        $domain = ResourceSite::getDomain($site->value);
    }

    $url = 'https://'.$domain;

    $attribute = fake()->word();

    $validator = Validator::make(
        [$attribute => $site->value],
        [$attribute => new ResourceSiteMatchesLinkRule($url)],
    );

    expect($validator->passes())->toBeTrue();
});

test('resource site domain rule official passes', function (): void {
    $attribute = fake()->word();

    $validator = Validator::make(
        [$attribute => ResourceSite::OFFICIAL_SITE->value],
        [$attribute => new ResourceSiteMatchesLinkRule(fake()->url())],
    );

    expect($validator->passes())->toBeTrue();
});

test('resource site domain rule fails', function (): void {
    $site = null;

    while (true) {
        $site = Arr::random(ResourceSite::cases());

        if (ResourceSite::getDomain($site->value)) {
            break;
        }
    }

    $attribute = fake()->word();

    $validator = Validator::make(
        [$attribute => $site->value],
        [$attribute => new ResourceSiteMatchesLinkRule(fake()->url())],
    );

    expect($validator->passes())->toBeFalse();
});
