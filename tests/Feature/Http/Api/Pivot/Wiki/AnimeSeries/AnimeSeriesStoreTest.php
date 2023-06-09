<?php

declare(strict_types=1);

namespace Http\Api\Pivot\Wiki\AnimeSeries;

use App\Enums\Auth\CrudPermission;
use App\Models\Auth\User;
use App\Models\Wiki\Anime;
use App\Models\Wiki\Series;
use App\Pivots\Wiki\AnimeSeries;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

/**
 * Class AnimeSeriesStoreTest.
 */
class AnimeSeriesStoreTest extends TestCase
{
    /**
     * The Anime Series Store Endpoint shall be protected by sanctum.
     *
     * @return void
     */
    public function testProtected(): void
    {
        $anime = Anime::factory()->createOne();
        $series = Series::factory()->createOne();

        $response = $this->post(route('api.animeseries.store', ['anime' => $anime, 'series' => $series]));

        $response->assertUnauthorized();
    }

    /**
     * The Anime Series Store Endpoint shall forbid users without the create anime & create series permissions.
     *
     * @return void
     */
    public function testForbidden(): void
    {
        $anime = Anime::factory()->createOne();
        $series = Series::factory()->createOne();

        $user = User::factory()->createOne();

        Sanctum::actingAs($user);

        $response = $this->post(route('api.animeseries.store', ['anime' => $anime, 'series' => $series]));

        $response->assertForbidden();
    }

    /**
     * The Anime Series Store Endpoint shall create an anime series.
     *
     * @return void
     */
    public function testCreate(): void
    {
        $anime = Anime::factory()->createOne();
        $series = Series::factory()->createOne();

        $user = User::factory()
            ->withPermissions(
                CrudPermission::CREATE->format(Anime::class),
                CrudPermission::CREATE->format(Series::class)
            )
            ->createOne();

        Sanctum::actingAs($user);

        $response = $this->post(route('api.animeseries.store', ['anime' => $anime, 'series' => $series]));

        $response->assertCreated();
        static::assertDatabaseCount(AnimeSeries::class, 1);
    }
}
