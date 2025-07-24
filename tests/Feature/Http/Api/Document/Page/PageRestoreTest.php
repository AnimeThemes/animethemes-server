<?php

declare(strict_types=1);

namespace Tests\Feature\Http\Api\Document\Page;

use App\Enums\Auth\ExtendedCrudPermission;
use App\Models\Auth\User;
use App\Models\Document\Page;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class PageRestoreTest extends TestCase
{
    /**
     * The Page Restore Endpoint shall be protected by sanctum.
     */
    public function testProtected(): void
    {
        $page = Page::factory()->trashed()->createOne();

        $response = $this->patch(route('api.page.restore', ['page' => $page]));

        $response->assertUnauthorized();
    }

    /**
     * The Page Restore Endpoint shall forbid users without the restore page permission.
     */
    public function testForbidden(): void
    {
        $page = Page::factory()->trashed()->createOne();

        $user = User::factory()->createOne();

        Sanctum::actingAs($user);

        $response = $this->patch(route('api.page.restore', ['page' => $page]));

        $response->assertForbidden();
    }

    /**
     * The Page Restore Endpoint shall forbid users from restoring a page that isn't trashed.
     */
    public function testTrashed(): void
    {
        $page = Page::factory()->createOne();

        $user = User::factory()->withPermissions(ExtendedCrudPermission::RESTORE->format(Page::class))->createOne();

        Sanctum::actingAs($user);

        $response = $this->patch(route('api.page.restore', ['page' => $page]));

        $response->assertForbidden();
    }

    /**
     * The Page Restore Endpoint shall restore the page.
     */
    public function testRestored(): void
    {
        $page = Page::factory()->trashed()->createOne();

        $user = User::factory()->withPermissions(ExtendedCrudPermission::RESTORE->format(Page::class))->createOne();

        Sanctum::actingAs($user);

        $response = $this->patch(route('api.page.restore', ['page' => $page]));

        $response->assertOk();
        static::assertNotSoftDeleted($page);
    }
}
