<?php

declare(strict_types=1);

namespace Tests\Feature\Http\Api\Document\Page;

use App\Enums\Auth\ExtendedCrudPermission;
use App\Models\Auth\User;
use App\Models\Document\Page;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class PageForceDeleteTest extends TestCase
{
    /**
     * The Page Force Delete Endpoint shall be protected by sanctum.
     */
    public function testProtected(): void
    {
        $page = Page::factory()->createOne();

        $response = $this->delete(route('api.page.forceDelete', ['page' => $page]));

        $response->assertUnauthorized();
    }

    /**
     * The Page Force Delete Endpoint shall forbid users without the force delete page permission.
     */
    public function testForbidden(): void
    {
        $page = Page::factory()->createOne();

        $user = User::factory()->createOne();

        Sanctum::actingAs($user);

        $response = $this->delete(route('api.page.forceDelete', ['page' => $page]));

        $response->assertForbidden();
    }

    /**
     * The Page Force Delete Endpoint shall force delete the page.
     */
    public function testDeleted(): void
    {
        $page = Page::factory()->createOne();

        $user = User::factory()->withPermissions(ExtendedCrudPermission::FORCE_DELETE->format(Page::class))->createOne();

        Sanctum::actingAs($user);

        $response = $this->delete(route('api.page.forceDelete', ['page' => $page]));

        $response->assertOk();
        static::assertModelMissing($page);
    }
}
