<?php

declare(strict_types=1);

namespace Tests\Feature\Http\Document;

use Tests\TestCase;

/**
 * Class CommunityShowTest.
 */
class CommunityShowTest extends TestCase
{
    /**
     * The Community Bitrate effort shall be displayed as a document.
     *
     * @return void
     */
    public function testBitrate(): void
    {
        $response = $this->get(route('community.show', ['docName' => 'bitrate']));

        $response->assertViewIs('document');
    }

    /**
     * The Community Pixel Format effort shall be displayed as a document.
     *
     * @return void
     */
    public function testPixFmt(): void
    {
        $response = $this->get(route('community.show', ['docName' => 'pix_fmt']));

        $response->assertViewIs('document');
    }

    /**
     * The Community Requests effort shall be displayed as a document.
     *
     * @return void
     */
    public function testRequests(): void
    {
        $response = $this->get(route('community.show', ['docName' => 'requests']));

        $response->assertViewIs('document');
    }

    /**
     * The Community VP9 effort shall be displayed as a document.
     *
     * @return void
     */
    public function testVP9(): void
    {
        $response = $this->get(route('community.show', ['docName' => 'vp9']));

        $response->assertViewIs('document');
    }
}
