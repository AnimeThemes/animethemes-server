<?php

declare(strict_types=1);

namespace Tests\Feature\Http\Api\Document\Page;

use App\Models\Auth\User;
use App\Models\Document\Page;
use Illuminate\Foundation\Testing\WithoutEvents;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

/**
 * Class PageUpdateTest.
 */
class PageUpdateTest extends TestCase
{
    use WithoutEvents;

    /**
     * The Page Update Endpoint shall be protected by sanctum.
     *
     * @return void
     */
    public function testProtected(): void
    {
        $page = Page::factory()->createOne();

        $parameters = Page::factory()->raw();

        $response = $this->put(route('api.page.update', ['page' => $page] + $parameters));

        $response->assertUnauthorized();
    }

    /**
     * The Page Update Endpoint shall update an page.
     *
     * @return void
     */
    public function testUpdate(): void
    {
        $page = Page::factory()->createOne();

        $parameters = Page::factory()->raw();

        $user = User::factory()->withPermission('update page')->createOne();

        Sanctum::actingAs($user);

        $response = $this->put(route('api.page.update', ['page' => $page] + $parameters));

        $response->assertOk();
    }
}
