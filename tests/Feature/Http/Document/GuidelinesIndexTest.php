<?php

declare(strict_types=1);

namespace Http\Document;

use Tests\TestCase;

/**
 * Class GuidelinesIndexTest
 * @package Http\Document
 */
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
