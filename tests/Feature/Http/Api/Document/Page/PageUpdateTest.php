<?php

declare(strict_types=1);

namespace Tests\Feature\Http\Api\Document\Page;

use App\Enums\Auth\CrudPermission;
use App\Models\Auth\User;
use App\Models\Document\Page;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class PageUpdateTest extends TestCase
{
    /**
     * The Page Update Endpoint shall be protected by sanctum.
     */
    public function testProtected(): void
    {
        $page = Page::factory()->createOne();

        $parameters = Page::factory()->raw();

        $response = $this->put(route('api.page.update', ['page' => $page] + $parameters));

        $response->assertUnauthorized();
    }

    /**
     * The Page Update Endpoint shall forbid users without the update page permission.
     */
    public function testForbidden(): void
    {
        $page = Page::factory()->createOne();

        $parameters = Page::factory()->raw();

        $user = User::factory()->createOne();

        Sanctum::actingAs($user);

        $response = $this->put(route('api.page.update', ['page' => $page] + $parameters));

        $response->assertForbidden();
    }

    /**
     * The Page Update Endpoint shall forbid users from updating a page that is trashed.
     */
    public function testTrashed(): void
    {
        $page = Page::factory()->trashed()->createOne();

        $parameters = Page::factory()->raw();

        $user = User::factory()->withPermissions(CrudPermission::UPDATE->format(Page::class))->createOne();

        Sanctum::actingAs($user);

        $response = $this->put(route('api.page.update', ['page' => $page] + $parameters));

        $response->assertForbidden();
    }

    /**
     * The Page Update Endpoint shall update an page.
     */
    public function testUpdate(): void
    {
        $page = Page::factory()->createOne();

        $parameters = Page::factory()->raw();

        $user = User::factory()->withPermissions(CrudPermission::UPDATE->format(Page::class))->createOne();

        Sanctum::actingAs($user);

        $response = $this->put(route('api.page.update', ['page' => $page] + $parameters));

        $response->assertOk();
    }
}
