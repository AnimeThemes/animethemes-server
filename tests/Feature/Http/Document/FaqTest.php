<?php

namespace Tests\Feature\Http\Document;

use Tests\TestCase;

class FaqTest extends TestCase
{
    /**
     * The FAQ shall be displayed as a document.
     *
     * @return void
     */
    public function testView()
    {
        $response = $this->get(route('faq.show'));

        $response->assertViewIs('document');
    }
}
