<?php declare(strict_types=1);

namespace Http\Sitemap;

use Tests\TestCase;

/**
 * Class EventSitemapTest
 * @package Http\Sitemap
 */
class EventSitemapTest extends TestCase
{
    /**
     * The event sitemap shall display the event sitemap view.
     *
     * @return void
     */
    public function testSitemapIndex()
    {
        $response = $this->get(route('sitemap.event'));

        $response->assertViewIs('sitemap.event');
    }

    /**
     * The event sitemap shall display the event index route.
     *
     * @return void
     */
    public function testIndex()
    {
        $response = $this->get(route('sitemap.event'));

        $response->assertSee(route('event.index'));
    }

    /**
     * The event sitemap shall display the event best_ending_iii route.
     *
     * @return void
     */
    public function testBestEndingIII()
    {
        $response = $this->get(route('sitemap.event'));

        $response->assertSee(route('event.show', ['docName' => 'best_ending_iii']));
    }

    /**
     * The event sitemap shall display the event best_ending_iv route.
     *
     * @return void
     */
    public function testBestEndingIV()
    {
        $response = $this->get(route('sitemap.event'));

        $response->assertSee(route('event.show', ['docName' => 'best_ending_iv']));
    }

    /**
     * The event sitemap shall display the event best_ending_v route.
     *
     * @return void
     */
    public function testBestEndingV()
    {
        $response = $this->get(route('sitemap.event'));

        $response->assertSee(route('event.show', ['docName' => 'best_ending_v']));
    }

    /**
     * The event sitemap shall display the event best_ending_vi route.
     *
     * @return void
     */
    public function testBestEndingVI()
    {
        $response = $this->get(route('sitemap.event'));

        $response->assertSee(route('event.show', ['docName' => 'best_ending_vi']));
    }

    /**
     * The event sitemap shall display the event best_opening_vii route.
     *
     * @return void
     */
    public function testBestOpeningVII()
    {
        $response = $this->get(route('sitemap.event'));

        $response->assertSee(route('event.show', ['docName' => 'best_opening_vii']));
    }

    /**
     * The event sitemap shall display the event best_opening_viii route.
     *
     * @return void
     */
    public function testBestOpeningVIII()
    {
        $response = $this->get(route('sitemap.event'));

        $response->assertSee(route('event.show', ['docName' => 'best_opening_viii']));
    }
}
