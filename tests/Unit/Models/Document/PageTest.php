<?php

declare(strict_types=1);

namespace Tests\Unit\Models\Document;

use App\Models\Document\Page;
use Illuminate\Support\Facades\Config;
use Tests\TestCase;

/**
 * Class PageTest.
 */
class PageTest extends TestCase
{
    /**
     * Page shall be auditable.
     *
     * @return void
     */
    public function testAuditable(): void
    {
        Config::set('audit.console', true);

        $page = Page::factory()->createOne();

        static::assertEquals(1, $page->audits()->count());
    }

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
}
