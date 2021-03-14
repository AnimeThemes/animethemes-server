<?php

namespace Tests\Feature\Http;

use Tests\TestCase;

class SitemapTest extends TestCase
{
    /**
     * The sitemap shall display the Welcome route.
     *
     * @return void
     */
    public function testSitemapIndex()
    {
        $response = $this->get(route('sitemap.index'));

        $response->assertViewIs('sitemap.index');
    }

    /**
     * The sitemap shall display the Welcome route.
     *
     * @return void
     */
    public function testSitemapIndexWelcome()
    {
        $response = $this->get(route('sitemap.index'));

        $response->assertSee(route('welcome.index'));
    }

    /**
     * The sitemap shall display the API Docs route.
     *
     * @return void
     */
    public function testSitemapIndexApi()
    {
        $response = $this->get(route('sitemap.index'));

        $response->assertSee(route('l5-swagger.default.api'));
    }
}
