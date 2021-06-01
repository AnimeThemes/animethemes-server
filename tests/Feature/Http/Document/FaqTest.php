<?php

declare(strict_types=1);

namespace Http\Document;

use Tests\TestCase;

/**
 * Class FaqTest
 * @package Http\Document
 */
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
