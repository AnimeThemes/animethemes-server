<?php declare(strict_types=1);

namespace Http\Document;

use Tests\TestCase;

/**
 * Class CommunityIndexTest
 * @package Http\Document
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
