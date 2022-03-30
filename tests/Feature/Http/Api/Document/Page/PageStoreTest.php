<?php

declare(strict_types=1);

namespace Tests\Feature\Http\Api\Document\Page;

use App\Models\Auth\User;
use App\Models\Document\Page;
use Illuminate\Foundation\Testing\WithoutEvents;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

/**
 * Class PageStoreTest.
 */
class PageStoreTest extends TestCase
{
    use WithoutEvents;

    /**
     * The Page Store Endpoint shall be protected by sanctum.
     *
     * @return void
     */
    public function testProtected(): void
    {
        $page = Page::factory()->makeOne();

        $response = $this->post(route('api.page.store', $page->toArray()));

        $response->assertForbidden();
    }

    /**
     * The Page Store Endpoint shall require body, name & slug fields.
     *
     * @return void
     */
    public function testRequiredFields(): void
    {
        Sanctum::actingAs(
            User::factory()->withCurrentTeam('editor')->createOne(),
            ['page:create']
        );

        $response = $this->post(route('api.page.store'));

        $response->assertJsonValidationErrors([
            Page::ATTRIBUTE_BODY,
            Page::ATTRIBUTE_NAME,
            Page::ATTRIBUTE_SLUG,
        ]);
    }

    /**
     * The Page Store Endpoint shall create a page.
     *
     * @return void
     */
    public function testCreate(): void
    {
        $parameters = Page::factory()->raw();

        Sanctum::actingAs(
            User::factory()->withCurrentTeam('editor')->createOne(),
            ['page:create']
        );

        $response = $this->post(route('api.page.store', $parameters));

        $response->assertCreated();
        static::assertDatabaseCount(Page::TABLE, 1);
    }
}
