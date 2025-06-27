<?php

declare(strict_types=1);

namespace Tests\Unit\Models\Document;

use App\Models\Document\Page;
use Tests\TestCase;

/**
 * Class PageTest.
 */
class PageTest extends TestCase
{
    /**
     * Pages shall be nameable.
     *
     * @return void
     */
    public function testNameable(): void
    {
        $page = Page::factory()->createOne();

        static::assertIsString($page->getName());
    }

    /**
     * Pages shall have subtitle.
     *
     * @return void
     */
    public function testHasSubtitle(): void
    {
        $page = Page::factory()->createOne();

        static::assertIsString($page->getSubtitle());
    }
}
