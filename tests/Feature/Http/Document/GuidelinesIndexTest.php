<?php

namespace Tests\Feature\Http\Document;

use Tests\TestCase;

class GuidelinesIndexTest extends TestCase
{
    /**
     * The Guidelines Index shall be displayed as a document.
     *
     * @return void
     */
    public function testView()
    {
        $response = $this->get(route('guidelines.index'));

        $response->assertViewIs('document');
    }
}
