<?php

namespace Tests\Unit\Rules;

use App\Enums\ResourceSite;
use App\Rules\ResourceSiteDomainRule;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class ResourceSiteDomainRuleTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    /**
     * The Resource Site Domain Rule shall return true if the link matches the site.
     *
     * @return void
     */
    public function testResourceSiteDomainRulePasses()
    {
        $site = ResourceSite::getRandomInstance();

        $url = null;
        $domain = ResourceSite::getDomain($site->value);
        if ($domain !== null) {
            $url = 'https://'.$domain;
        }

        $rule = new ResourceSiteDomainRule($site->value);

        $this->assertTrue($rule->passes($this->faker->word(), $url));
    }

    /**
     * The Resource Site Domain Rule shall return true if the site does not have a domain.
     *
     * @return void
     */
    public function testResourceSiteDomainRuleOfficialPasses()
    {
        $site = ResourceSite::OFFICIAL_SITE;

        $rule = new ResourceSiteDomainRule($site);

        $this->assertTrue($rule->passes($this->faker->word(), $this->faker->url));
    }

    /**
     * The Resource Site Domain Rule shall return false if the link does not match the site.
     *
     * @return void
     */
    public function testResourceSiteDomainRuleFails()
    {
        $site = null;

        while ($site === null) {
            $site_candidate = ResourceSite::getRandomInstance();
            if (! $site_candidate->is(ResourceSite::OFFICIAL_SITE)) {
                $site = $site_candidate;
            }
        }

        $rule = new ResourceSiteDomainRule($site->value);

        $this->assertFalse($rule->passes($this->faker->word(), $this->faker->url));
    }
}
