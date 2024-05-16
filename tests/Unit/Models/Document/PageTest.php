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
     * Pages shall be subnameable.
     *
     * @return void
     */
    public function testSubNameable(): void
    {
        $page = Page::factory()->createOne();

        static::assertIsString($page->getSubName());
    }
}
