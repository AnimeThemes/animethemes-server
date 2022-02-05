<?php

declare(strict_types=1);

namespace Tests\Feature\Http\Document;

use Tests\TestCase;

/**
 * Class GuidelinesIndexTest.
 */
class GuidelinesIndexTest extends TestCase
{
    /**
     * The Guidelines Index shall be displayed as a document.
     *
     * @return void
     */
    public function testView(): void
    {
        $response = $this->get(route('guidelines.index'));

        $response->assertViewIs('document');
    }
}
