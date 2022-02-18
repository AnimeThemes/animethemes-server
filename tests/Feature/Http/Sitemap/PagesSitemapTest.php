<?php

declare(strict_types=1);

namespace Tests\Feature\Http\Sitemap;

use App\Models\Document\Page;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\WithoutEvents;
use Tests\TestCase;

/**
 * Class PagesSitemapTest.
 */
class PagesSitemapTest extends TestCase
{
    use WithFaker;
    use WithoutEvents;

    /**
     * The pages sitemap index shall display the sitemap pages view.
     *
     * @return void
     */
    public function testView(): void
    {
        $response = $this->get(route('sitemap.pages'));

        $response->assertViewIs('sitemap.pages');
    }

    /**
     * The pages sitemap index shall display pages show route links.
     *
     * @return void
     */
    public function testLinks(): void
    {
        $pages = Page::factory()->count($this->faker->randomDigitNotNull())->create();

        $response = $this->get(route('sitemap.pages'));

        foreach ($pages as $page) {
            $response->assertSee(route('page.show', ['page' => $page]));
        }
    }
}
