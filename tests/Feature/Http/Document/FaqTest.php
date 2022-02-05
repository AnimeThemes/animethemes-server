<?php

declare(strict_types=1);

namespace Tests\Feature\Http\Document;

use Tests\TestCase;

/**
 * Class FaqTest.
 */
class FaqTest extends TestCase
{
    /**
     * The FAQ shall be displayed as a document.
     *
     * @return void
     */
    public function testView(): void
    {
        $response = $this->get(route('faq.show'));

        $response->assertViewIs('document');
    }
}
