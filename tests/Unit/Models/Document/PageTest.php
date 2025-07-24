<?php

declare(strict_types=1);

namespace Tests\Unit\Models\Document;

use App\Models\Document\Page;
use Tests\TestCase;

class PageTest extends TestCase
{
    /**
     * Pages shall be nameable.
     */
    public function testNameable(): void
    {
        $page = Page::factory()->createOne();

        static::assertIsString($page->getName());
    }

    /**
     * Pages shall have subtitle.
     */
    public function testHasSubtitle(): void
    {
        $page = Page::factory()->createOne();

        static::assertIsString($page->getSubtitle());
    }
}
