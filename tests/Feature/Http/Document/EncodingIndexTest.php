<?php

declare(strict_types=1);

namespace Tests\Feature\Http\Document;

use Tests\TestCase;

/**
 * Class EncodingIndexTest.
 */
class EncodingIndexTest extends TestCase
{
    /**
     * The Encoding Index shall be displayed as a document.
     *
     * @return void
     */
    public function testView(): void
    {
        $response = $this->get(route('encoding.index'));

        $response->assertViewIs('document');
    }
}
