<?php

declare(strict_types=1);

namespace Http\Document;

use Tests\TestCase;

/**
 * Class DonateTest.
 */
class DonateTest extends TestCase
{
    /**
     * The Donate Page shall be displayed as a document.
     *
     * @return void
     */
    public function testView()
    {
        $response = $this->get(route('donate.show'));

        $response->assertViewIs('document');
    }
}
