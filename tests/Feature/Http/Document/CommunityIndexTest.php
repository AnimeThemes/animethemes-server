<?php

namespace Tests\Feature\Http\Document;

use Tests\TestCase;

class CommunityIndexTest extends TestCase
{
    /**
     * The Community Index shall be displayed as a document.
     *
     * @return void
     */
    public function testView()
    {
        $response = $this->get(route('community.index'));

        $response->assertViewIs('document');
    }
}
