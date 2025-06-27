<?php

declare(strict_types=1);

namespace Tests\Feature\Http\Api\Document\Page;

use App\Enums\Auth\CrudPermission;
use App\Models\Auth\User;
use App\Models\Document\Page;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

/**
 * Class PageDestroyTest.
 */
class PageDestroyTest extends TestCase
{
    /**
     * The Page Destroy Endpoint shall be protected by sanctum.
     *
     * @return void
     */
    public function testProtected(): void
    {
        $page = Page::factory()->createOne();

        $response = $this->delete(route('api.page.destroy', ['page' => $page]));

        $response->assertUnauthorized();
    }

    /**
     * The Page Destroy Endpoint shall forbid users without the delete page permission.
     *
     * @return void
     */
    public function testForbidden(): void
    {
        $page = Page::factory()->createOne();

        $user = User::factory()->createOne();

        Sanctum::actingAs($user);

        $response = $this->delete(route('api.page.destroy', ['page' => $page]));

        $response->assertForbidden();
    }

    /**
     * The Page Destroy Endpoint shall forbid users from updating a page that is trashed.
     *
     * @return void
     */
    public function testTrashed(): void
    {
        $page = Page::factory()->trashed()->createOne();

        $user = User::factory()->withPermissions(CrudPermission::DELETE->format(Page::class))->createOne();

        Sanctum::actingAs($user);

        $response = $this->delete(route('api.page.destroy', ['page' => $page]));

        $response->assertNotFound();
    }

    /**
     * The Page Destroy Endpoint shall delete the page.
     *
     * @return void
     */
    public function testDeleted(): void
    {
        $page = Page::factory()->createOne();

        $user = User::factory()->withPermissions(CrudPermission::DELETE->format(Page::class))->createOne();

        Sanctum::actingAs($user);

        $response = $this->delete(route('api.page.destroy', ['page' => $page]));

        $response->assertOk();
        static::assertSoftDeleted($page);
    }
}
