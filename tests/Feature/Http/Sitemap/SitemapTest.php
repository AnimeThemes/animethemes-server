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
    public function testSitemapIndex(): void
    {
        $response = $this->get(route('sitemap'));

        $response->assertViewIs('sitemap.index');
    }

    /**
     * The sitemap index shall display the policy route.
     *
     * @return void
     */
    public function testPolicy(): void
    {
        $response = $this->get(route('sitemap'));

        $response->assertSee(route('policy.show'));
    }

    /**
     * The sitemap index shall display the terms route.
     *
     * @return void
     */
    public function testTerms(): void
    {
        $response = $this->get(route('sitemap'));

        $response->assertSee(route('terms.show'));
    }

    /**
     * The sitemap index shall display the terms route.
     *
     * @return void
     */
    public function testTransparency(): void
    {
        $response = $this->get(route('sitemap'));

        $response->assertSee(route('transparency.show'));
    }

    /**
     * The sitemap index shall display the welcome route.
     *
     * @return void
     */
    public function testWelcome(): void
    {
        $response = $this->get(route('sitemap'));

        $response->assertSee(route('welcome'));
    }

    /**
     * The sitemap index shall display the wiki route.
     *
     * @return void
     */
    public function testWiki(): void
    {
        $response = $this->get(route('sitemap'));

        $response->assertSee(url('wiki'));
    }
}
