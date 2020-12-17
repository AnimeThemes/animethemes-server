<?php

namespace Tests\Feature\Http;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class SitemapTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    /**
     * The sitemap shall display the Welcome route.
     *
     * @return void
     */
    public function testSitemapIndex()
    {
        $response = $this->get(route('sitemap'));

        $response->assertViewIs('sitemap');
    }

    /**
     * The sitemap shall display the Welcome route.
     *
     * @return void
     */
    public function testSitemapIndexWelcome()
    {
        $response = $this->get(route('sitemap'));

        $response->assertSee(route('welcome'));
    }

    /**
     * The sitemap shall display the API Docs route.
     *
     * @return void
     */
    public function testSitemapIndexApi()
    {
        $response = $this->get(route('sitemap'));

        $response->assertSee(route('l5-swagger.default.api'));
    }
}
