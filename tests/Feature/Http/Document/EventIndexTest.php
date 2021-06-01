<?php declare(strict_types=1);

namespace Http\Document;

use Tests\TestCase;

/**
 * Class EventIndexTest
 * @package Http\Document
 */
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
