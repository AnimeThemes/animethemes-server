<?php

declare(strict_types=1);

namespace Tests\Feature\Http\Document;

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
    public function testBestEndingIII(): void
    {
        $response = $this->get(route('event.show', ['docName' => 'best_ending_iii']));

        $response->assertViewIs('document');
    }

    /**
     * Best Ending IV shall be displayed as a document.
     *
     * @return void
     */
    public function testBestEndingIV(): void
    {
        $response = $this->get(route('event.show', ['docName' => 'best_ending_iv']));

        $response->assertViewIs('document');
    }

    /**
     * Best Ending V shall be displayed as a document.
     *
     * @return void
     */
    public function testBestEndingV(): void
    {
        $response = $this->get(route('event.show', ['docName' => 'best_ending_v']));

        $response->assertViewIs('document');
    }

    /**
     * Best Ending VI shall be displayed as a document.
     *
     * @return void
     */
    public function testBestEndingVI(): void
    {
        $response = $this->get(route('event.show', ['docName' => 'best_ending_vi']));

        $response->assertViewIs('document');
    }

    /**
     * Best Opening VII shall be displayed as a document.
     *
     * @return void
     */
    public function testBestOpeningVII(): void
    {
        $response = $this->get(route('event.show', ['docName' => 'best_opening_vii']));

        $response->assertViewIs('document');
    }

    /**
     * Best Opening VIII shall be displayed as a document.
     *
     * @return void
     */
    public function testBestOpeningVIII(): void
    {
        $response = $this->get(route('event.show', ['docName' => 'best_opening_viii']));

        $response->assertViewIs('document');
    }

    /**
     * Best Opening IX shall be displayed as a document.
     *
     * @return void
     */
    public function testBestOpeningIX(): void
    {
        $response = $this->get(route('event.show', ['docName' => 'best_opening_ix']));

        $response->assertViewIs('document');
    }
}
