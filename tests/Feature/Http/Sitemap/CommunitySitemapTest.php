<?php

declare(strict_types=1);

namespace Http\Sitemap;

use Tests\TestCase;

/**
 * Class CommunitySitemapTest.
 */
class CommunitySitemapTest extends TestCase
{
    /**
     * The community sitemap shall display the community sitemap view.
     *
     * @return void
     */
    public function testSitemapIndex()
    {
        $response = $this->get(route('sitemap.community'));

        $response->assertViewIs('sitemap.community');
    }

    /**
     * The community sitemap shall display the community index route.
     *
     * @return void
     */
    public function testIndex()
    {
        $response = $this->get(route('sitemap.community'));

        $response->assertSee(route('community.index'));
    }

    /**
     * The community sitemap shall display the community bitrate route.
     *
     * @return void
     */
    public function testBitrate()
    {
        $response = $this->get(route('sitemap.community'));

        $response->assertSee(route('community.show', ['docName' => 'bitrate']));
    }

    /**
     * The community sitemap shall display the community pix_fmt route.
     *
     * @return void
     */
    public function testPixFmt()
    {
        $response = $this->get(route('sitemap.community'));

        $response->assertSee(route('community.show', ['docName' => 'pix_fmt']));
    }

    /**
     * The community sitemap shall display the community requests route.
     *
     * @return void
     */
    public function testRequests()
    {
        $response = $this->get(route('sitemap.community'));

        $response->assertSee(route('community.show', ['docName' => 'requests']));
    }

    /**
     * The community sitemap shall display the community VP9 route.
     *
     * @return void
     */
    public function testVP9()
    {
        $response = $this->get(route('sitemap.community'));

        $response->assertSee(route('community.show', ['docName' => 'vp9']));
    }
}
