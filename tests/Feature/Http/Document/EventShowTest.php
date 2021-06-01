<?php

declare(strict_types=1);

namespace Http\Document;

use Tests\TestCase;

/**
 * Class EventShowTest.
 */
class EventShowTest extends TestCase
{
    /**
     * Best Ending III shall be displayed as a document.
     *
     * @return void
     */
    public function testBestEndingIII()
    {
        $response = $this->get(route('event.show', ['docName' => 'best_ending_iii']));

        $response->assertViewIs('document');
    }

    /**
     * Best Ending IV shall be displayed as a document.
     *
     * @return void
     */
    public function testBestEndingIV()
    {
        $response = $this->get(route('event.show', ['docName' => 'best_ending_iv']));

        $response->assertViewIs('document');
    }

    /**
     * Best Ending V shall be displayed as a document.
     *
     * @return void
     */
    public function testBestEndingV()
    {
        $response = $this->get(route('event.show', ['docName' => 'best_ending_v']));

        $response->assertViewIs('document');
    }

    /**
     * Best Ending VI shall be displayed as a document.
     *
     * @return void
     */
    public function testBestEndingVI()
    {
        $response = $this->get(route('event.show', ['docName' => 'best_ending_vi']));

        $response->assertViewIs('document');
    }

    /**
     * Best Opening VII shall be displayed as a document.
     *
     * @return void
     */
    public function testBestOpeningVII()
    {
        $response = $this->get(route('event.show', ['docName' => 'best_opening_vii']));

        $response->assertViewIs('document');
    }

    /**
     * Best Opening VIII shall be displayed as a document.
     *
     * @return void
     */
    public function testBestOpeningVIII()
    {
        $response = $this->get(route('event.show', ['docName' => 'best_opening_viii']));

        $response->assertViewIs('document');
    }
}
