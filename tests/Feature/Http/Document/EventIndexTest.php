<?php

namespace Tests\Feature\Http\Document;

use Tests\TestCase;

class EventIndexTest extends TestCase
{
    /**
     * The Event Index shall be displayed as a document.
     *
     * @return void
     */
    public function testView()
    {
        $response = $this->get(route('event.index'));

        $response->assertViewIs('document');
    }
}
