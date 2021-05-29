<?php

namespace Tests\Feature\Http\Document;

use Tests\TestCase;

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
