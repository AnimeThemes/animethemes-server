<?php

declare(strict_types=1);

namespace Tests\Unit\Rules\Wiki\Resource;

use App\Enums\Models\Wiki\ResourceSite;
use App\Rules\Wiki\Resource\ResourceSiteMatchesLinkRule;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Validator;
use Tests\TestCase;

class ResourceSiteMatchesLinkTest extends TestCase
{
    use WithFaker;

    /**
     * The Resource Site Matches Link Rule shall return true if the site matches the link.
     */
    public function testPassesIfSiteMatchesLink(): void
    {
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

        $attribute = $this->faker->word();

        $validator = Validator::make(
            [$attribute => $site->value],
            [$attribute => new ResourceSiteMatchesLinkRule($url)],
        );

        static::assertTrue($validator->passes());
    }

    /**
     * The Resource Site Matches Link Rule shall return true if the site does not have a domain.
     */
    public function testResourceSiteDomainRuleOfficialPasses(): void
    {
        $attribute = $this->faker->word();

        $validator = Validator::make(
            [$attribute => ResourceSite::OFFICIAL_SITE->value],
            [$attribute => new ResourceSiteMatchesLinkRule($this->faker->url())],
        );

        static::assertTrue($validator->passes());
    }

    /**
     * The Resource Site Matches Link Rule shall return false if the site does not match the link.
     */
    public function testResourceSiteDomainRuleFails(): void
    {
        $site = null;

        while ($site === null) {
            $siteCandidate = Arr::random(ResourceSite::cases());
            if ($siteCandidate !== ResourceSite::OFFICIAL_SITE) {
                $site = $siteCandidate;
            }
        }

        $attribute = $this->faker->word();

        $validator = Validator::make(
            [$attribute => $site->value],
            [$attribute => new ResourceSiteMatchesLinkRule($this->faker->url())],
        );

        static::assertFalse($validator->passes());
    }
}
