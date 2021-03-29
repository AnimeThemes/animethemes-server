<?php

namespace Tests\Feature\Http\Document;

use Tests\TestCase;

class EncodingIndexTest extends TestCase
{
    /**
     * The Encoding Index shall be displayed as a document.
     *
     * @return void
     */
    public function testView()
    {
        $response = $this->get(route('encoding.index'));

        $response->assertViewIs('document');
    }
}
