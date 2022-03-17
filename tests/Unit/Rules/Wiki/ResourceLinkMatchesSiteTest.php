<?php

declare(strict_types=1);

namespace Tests\Unit\Rules\Wiki;

use App\Enums\Models\Wiki\ResourceSite;
use App\Rules\Wiki\ResourceLinkMatchesSiteRule;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

/**
 * Class ResourceSiteDomainRuleTest.
 */
class ResourceSiteDomainRuleTest extends TestCase
{
    use WithFaker;

    /**
     * The Resource Site Domain Rule shall return true if the link matches the site.
     *
     * @return void
     */
    public function testResourceSiteDomainRulePasses(): void
    {
        $site = ResourceSite::getRandomInstance();

        $url = null;
        $domain = ResourceSite::getDomain($site->value);
        if ($domain !== null) {
            $url = 'https://'.$domain;
        }

        $rule = new ResourceLinkMatchesSiteRule($site->value);

        static::assertTrue($rule->passes($this->faker->word(), $url));
    }

    /**
     * The Resource Site Domain Rule shall return true if the site does not have a domain.
     *
     * @return void
     */
    public function testResourceSiteDomainRuleOfficialPasses(): void
    {
        $site = ResourceSite::OFFICIAL_SITE;

        $rule = new ResourceLinkMatchesSiteRule($site);

        static::assertTrue($rule->passes($this->faker->word(), $this->faker->url()));
    }

    /**
     * The Resource Site Domain Rule shall return false if the link does not match the site.
     *
     * @return void
     */
    public function testResourceSiteDomainRuleFails(): void
    {
        $site = null;

        while ($site === null) {
            $siteCandidate = ResourceSite::getRandomInstance();
            if (! $siteCandidate->is(ResourceSite::OFFICIAL_SITE)) {
                $site = $siteCandidate;
            }
        }

        $rule = new ResourceLinkMatchesSiteRule($site->value);

        static::assertFalse($rule->passes($this->faker->word(), $this->faker->url()));
    }
}
