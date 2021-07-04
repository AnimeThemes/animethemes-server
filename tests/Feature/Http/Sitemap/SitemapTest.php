<?php

declare(strict_types=1);

namespace Tests\Feature\Http\Sitemap;

use Tests\TestCase;

/**
 * Class SitemapTest.
 */
class SitemapTest extends TestCase
{
    /**
     * The sitemap index shall display the sitemap index view.
     *
     * @return void
     */
    public function testSitemapIndex()
    {
        $response = $this->get(route('sitemap'));

        $response->assertViewIs('sitemap.index');
    }

    /**
     * The sitemap index shall display the donate route.
     *
     * @return void
     */
    public function testDonate()
    {
        $response = $this->get(route('sitemap'));

        $response->assertSee(route('donate.show'));
    }

    /**
     * The sitemap index shall display the FAQ route.
     *
     * @return void
     */
    public function testFaq()
    {
        $response = $this->get(route('sitemap'));

        $response->assertSee(route('faq.show'));
    }

    /**
     * The sitemap index shall display the policy route.
     *
     * @return void
     */
    public function testPolicy()
    {
        $response = $this->get(route('sitemap'));

        $response->assertSee(route('policy.show'));
    }

    /**
     * The sitemap index shall display the terms route.
     *
     * @return void
     */
    public function testTerms()
    {
        $response = $this->get(route('sitemap'));

        $response->assertSee(route('terms.show'));
    }

    /**
     * The sitemap index shall display the terms route.
     *
     * @return void
     */
    public function testTransparency()
    {
        $response = $this->get(route('sitemap'));

        $response->assertSee(route('transparency.show'));
    }

    /**
     * The sitemap index shall display the welcome route.
     *
     * @return void
     */
    public function testWelcome()
    {
        $response = $this->get(route('sitemap'));

        $response->assertSee(route('welcome'));
    }

    /**
     * The sitemap index shall display the wiki route.
     *
     * @return void
     */
    public function testWiki()
    {
        $response = $this->get(route('sitemap'));

        $response->assertSee(url('wiki'));
    }

    /**
     * The sitemap index shall display the community sitemap route.
     *
     * @return void
     */
    public function testCommunitySitemap()
    {
        $response = $this->get(route('sitemap'));

        $response->assertSee(route('sitemap.community'));
    }

    /**
     * The sitemap index shall display the encoding sitemap route.
     *
     * @return void
     */
    public function testEncodingSitemap()
    {
        $response = $this->get(route('sitemap'));

        $response->assertSee(route('sitemap.encoding'));
    }

    /**
     * The sitemap index shall display the event sitemap route.
     *
     * @return void
     */
    public function testEventSitemap()
    {
        $response = $this->get(route('sitemap'));

        $response->assertSee(route('sitemap.event'));
    }

    /**
     * The sitemap index shall display the guidelines sitemap route.
     *
     * @return void
     */
    public function testGuidelinesSitemap()
    {
        $response = $this->get(route('sitemap'));

        $response->assertSee(route('sitemap.guidelines'));
    }
}
