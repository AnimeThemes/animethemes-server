<?php

declare(strict_types=1);

namespace Tests\Feature\Http\Api\Document\Page;

use App\Models\Auth\User;
use App\Models\Document\Page;
use Illuminate\Foundation\Testing\WithoutEvents;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

/**
 * Class PageRestoreTest.
 */
class PageRestoreTest extends TestCase
{
    use WithoutEvents;

    /**
     * The Page Restore Endpoint shall be protected by sanctum.
     *
     * @return void
     */
    public function testProtected(): void
    {
        $page = Page::factory()->createOne();

        $page->delete();

        $response = $this->patch(route('api.page.restore', ['page' => $page]));

        $response->assertUnauthorized();
    }

    /**
     * The Page Restore Endpoint shall restore the page.
     *
     * @return void
     */
    public function testRestored(): void
    {
        $page = Page::factory()->createOne();

        $page->delete();

        $user = User::factory()->withPermission('restore page')->createOne();

        Sanctum::actingAs($user);

        $response = $this->patch(route('api.page.restore', ['page' => $page]));

        $response->assertOk();
        static::assertNotSoftDeleted($page);
    }
}
