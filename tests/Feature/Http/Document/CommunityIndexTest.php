<?php

declare(strict_types=1);

namespace Tests\Feature\Http\Document;

use Tests\TestCase;

/**
 * Class CommunityIndexTest.
 */
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
