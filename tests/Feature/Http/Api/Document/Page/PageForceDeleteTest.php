<?php

declare(strict_types=1);

namespace Tests\Feature\Http\Api\Document\Page;

use App\Models\Auth\User;
use App\Models\Document\Page;
use Illuminate\Foundation\Testing\WithoutEvents;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

/**
 * Class PageForceDeleteTest.
 */
class PageForceDeleteTest extends TestCase
{
    use WithoutEvents;

    /**
     * The Page Force Destroy Endpoint shall be protected by sanctum.
     *
     * @return void
     */
    public function testProtected(): void
    {
        $page = Page::factory()->createOne();

        $response = $this->delete(route('api.page.forceDelete', ['page' => $page]));

        $response->assertForbidden();
    }

    /**
     * The Page Force Destroy Endpoint shall force delete the page.
     *
     * @return void
     */
    public function testDeleted(): void
    {
        $page = Page::factory()->createOne();

        Sanctum::actingAs(
            User::factory()->withCurrentTeam('admin')->createOne(),
            ['*']
        );

        $response = $this->delete(route('api.page.forceDelete', ['page' => $page]));

        $response->assertOk();
        static::assertModelMissing($page);
    }
}
