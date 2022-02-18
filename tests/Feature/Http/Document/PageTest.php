<?php

declare(strict_types=1);

namespace Tests\Feature\Http\Document;

use App\Models\Document\Page;
use Illuminate\Foundation\Testing\WithoutEvents;
use Tests\TestCase;

/**
 * Class PageTest.
 */
class PageTest extends TestCase
{
    use WithoutEvents;

    /**
     * The page route shall display the document screen.
     *
     * @return void
     */
    public function testView(): void
    {
        $page = Page::factory()->createOne();

        $response = $this->get(route('page.show', ['page' => $page]));

        $response->assertViewIs('document');
    }

    /**
     * The page route shall display the page body.
     *
     * @return void
     */
    public function testSeeBody(): void
    {
        $page = Page::factory()->createOne();

        $response = $this->get(route('page.show', ['page' => $page]));

        $response->assertSee($page->body);
    }
}
