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
        if ($domain != null) {
            $url = 'https://'.$domain;
        }

        $rule = new ResourceSiteDomainRule($site->value);

        $this->assertTrue($rule->passes($this->faker->word(), $url));
    }

    /**
     * The Resource Site Domain Rule shall return false if the link does not match the site.
     *
     * @return void
     */
    public function testResourceSiteDomainRuleFails()
    {
        $site = ResourceSite::getRandomInstance();

        $rule = new ResourceSiteDomainRule($site->value);

        $this->assertFalse($rule->passes($this->faker->word(), $this->faker->url));
    }
}
