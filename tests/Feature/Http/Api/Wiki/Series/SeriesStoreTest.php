<?php

declare(strict_types=1);

namespace Tests\Feature\Http\Api\Wiki\Series;

use App\Enums\Auth\CrudPermission;
use App\Models\Auth\User;
use App\Models\Wiki\Series;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

/**
 * Class SeriesStoreTest.
 */
class SeriesStoreTest extends TestCase
{
    /**
     * The Series Store Endpoint shall be protected by sanctum.
     *
     * @return void
     */
    public function testProtected(): void
    {
        $series = Series::factory()->makeOne();

        $response = $this->post(route('api.series.store', $series->toArray()));

        $response->assertUnauthorized();
    }

    /**
     * The Series Store Endpoint shall forbid users without the create series permission.
     *
     * @return void
     */
    public function testForbidden(): void
    {
        $series = Series::factory()->makeOne();

        $user = User::factory()->createOne();

        Sanctum::actingAs($user);

        $response = $this->post(route('api.series.store', $series->toArray()));

        $response->assertForbidden();
    }

    /**
     * The Series Store Endpoint shall require name & slug fields.
     *
     * @return void
     */
    public function testRequiredFields(): void
    {
        $user = User::factory()->withPermissions(CrudPermission::CREATE()->format(Series::class))->createOne();

        Sanctum::actingAs($user);

        $response = $this->post(route('api.series.store'));

        $response->assertJsonValidationErrors([
            Series::ATTRIBUTE_NAME,
            Series::ATTRIBUTE_SLUG,
        ]);
    }

    /**
     * The Series Store Endpoint shall create a series.
     *
     * @return void
     */
    public function testCreate(): void
    {
        $parameters = Series::factory()->raw();

        $user = User::factory()->withPermissions(CrudPermission::CREATE()->format(Series::class))->createOne();

        Sanctum::actingAs($user);

        $response = $this->post(route('api.series.store', $parameters));

        $response->assertCreated();
        static::assertDatabaseCount(Series::class, 1);
    }
}
