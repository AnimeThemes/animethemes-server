<?php

declare(strict_types=1);

namespace Tests\Feature\Http\Document;

use Tests\TestCase;

/**
 * Class EventIndexTest.
 */
class EventIndexTest extends TestCase
{
    /**
     * The Event Index shall be displayed as a document.
     *
     * @return void
     */
    public function testView(): void
    {
        $response = $this->get(route('event.index'));

        $response->assertViewIs('document');
    }
}
