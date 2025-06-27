<?php

declare(strict_types=1);

namespace Tests\Feature\Http\Api\Document\Page;

use App\Enums\Auth\CrudPermission;
use App\Models\Auth\User;
use App\Models\Document\Page;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

/**
 * Class PageStoreTest.
 */
class PageStoreTest extends TestCase
{
    /**
     * The Page Store Endpoint shall be protected by sanctum.
     *
     * @return void
     */
    public function test_protected(): void
    {
        $page = Page::factory()->makeOne();

        $response = $this->post(route('api.page.store', $page->toArray()));

        $response->assertUnauthorized();
    }

    /**
     * The Page Store Endpoint shall forbid users without the create page permission.
     *
     * @return void
     */
    public function test_forbidden(): void
    {
        $page = Page::factory()->makeOne();

        $user = User::factory()->createOne();

        Sanctum::actingAs($user);

        $response = $this->post(route('api.page.store', $page->toArray()));

        $response->assertForbidden();
    }

    /**
     * The Page Store Endpoint shall require body, name & slug fields.
     *
     * @return void
     */
    public function test_required_fields(): void
    {
        $user = User::factory()->withPermissions(CrudPermission::CREATE->format(Page::class))->createOne();

        Sanctum::actingAs($user);

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
    public function test_create(): void
    {
        $parameters = Page::factory()->raw();

        $user = User::factory()->withPermissions(CrudPermission::CREATE->format(Page::class))->createOne();

        Sanctum::actingAs($user);

        $response = $this->post(route('api.page.store', $parameters));

        $response->assertCreated();
        static::assertDatabaseCount(Page::class, 1);
    }
}
