<?php

namespace Tests\Feature\Http;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SitemapTest extends TestCase
{
    use RefreshDatabase;

    /**
     * The sitemap index route shall display the index sitemap.
     *
     * @return void
     */
    public function testSitemapIndex()
    {
        $response = $this->get(route('sitemap.index'));

        $response->assertViewIs('sitemap.index');
    }
}
