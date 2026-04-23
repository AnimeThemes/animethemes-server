<?php

declare(strict_types=1);

use App\Enums\Models\Wiki\ResourceSite;
use App\Rules\Wiki\Resource\ResourceSiteMatchesLinkRule;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Validator;

uses(WithFaker::class);

test('passes if site matches link', function (): void {
    $site = null;

    while ($site === null) {
        $siteCandidate = Arr::random(ResourceSite::cases());
        if ($siteCandidate !== ResourceSite::OFFICIAL_SITE) {
            $site = $siteCandidate;
        }
    }

    $url = null;
    $domain = ResourceSite::getDomain($site->value);
    if ($domain !== null) {
        $url = 'https://'.$domain;
    }

    $attribute = fake()->word();

    $validator = Validator::make(
        [$attribute => $site->value],
        [$attribute => new ResourceSiteMatchesLinkRule($url)],
    );

    $this->assertTrue($validator->passes());
});

test('resource site domain rule official passes', function (): void {
    $attribute = fake()->word();

    $validator = Validator::make(
        [$attribute => ResourceSite::OFFICIAL_SITE->value],
        [$attribute => new ResourceSiteMatchesLinkRule(fake()->url())],
    );

    $this->assertTrue($validator->passes());
});

test('resource site domain rule fails', function (): void {
    $site = null;

    while ($site === null) {
        $siteCandidate = Arr::random(ResourceSite::cases());
        if ($siteCandidate !== ResourceSite::OFFICIAL_SITE) {
            $site = $siteCandidate;
        }
    }

    $attribute = fake()->word();

    $validator = Validator::make(
        [$attribute => $site->value],
        [$attribute => new ResourceSiteMatchesLinkRule(fake()->url())],
    );

    $this->assertFalse($validator->passes());
});
