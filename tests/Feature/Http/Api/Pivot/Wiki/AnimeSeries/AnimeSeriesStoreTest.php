<?php

declare(strict_types=1);

namespace Http\Api\Pivot\Wiki\AnimeSeries;

use App\Enums\Auth\CrudPermission;
use App\Models\Auth\User;
use App\Models\Wiki\Anime;
use App\Models\Wiki\Series;
use App\Pivots\Wiki\AnimeSeries;
use Illuminate\Foundation\Testing\WithoutEvents;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

/**
 * Class AnimeSeriesStoreTest.
 */
class AnimeSeriesStoreTest extends TestCase
{
    use WithoutEvents;

    /**
     * The Anime Series Store Endpoint shall be protected by sanctum.
     *
     * @return void
     */
    public function testProtected(): void
    {
        $animeSeries = AnimeSeries::factory()
            ->for(Anime::factory())
            ->for(Series::factory())
            ->makeOne();

        $response = $this->post(route('api.animeseries.store', $animeSeries->toArray()));

        $response->assertUnauthorized();
    }

    /**
     * The Anime Series Store Endpoint shall forbid users without the create anime & create series permissions.
     *
     * @return void
     */
    public function testForbidden(): void
    {
        $animeSeries = AnimeSeries::factory()
            ->for(Anime::factory())
            ->for(Series::factory())
            ->makeOne();

        $user = User::factory()->createOne();

        Sanctum::actingAs($user);

        $response = $this->post(route('api.animeseries.store', $animeSeries->toArray()));

        $response->assertForbidden();
    }

    /**
     * The Anime Series Store Endpoint shall require anime and series fields.
     *
     * @return void
     */
    public function testRequiredFields(): void
    {
        $user = User::factory()
            ->withPermissions(
                CrudPermission::CREATE()->format(Anime::class),
                CrudPermission::CREATE()->format(Series::class)
            )
            ->createOne();

        Sanctum::actingAs($user);

        $response = $this->post(route('api.animeseries.store'));

        $response->assertJsonValidationErrors([
            AnimeSeries::ATTRIBUTE_ANIME,
            AnimeSeries::ATTRIBUTE_SERIES,
        ]);
    }

    /**
     * The Anime Series Store Endpoint shall create an anime series.
     *
     * @return void
     */
    public function testCreate(): void
    {
        $parameters = [
            AnimeSeries::ATTRIBUTE_ANIME => Anime::factory()->createOne()->getKey(),
            AnimeSeries::ATTRIBUTE_SERIES => Series::factory()->createOne()->getKey(),
        ];

        $user = User::factory()
            ->withPermissions(
                CrudPermission::CREATE()->format(Anime::class),
                CrudPermission::CREATE()->format(Series::class)
            )
            ->createOne();

        Sanctum::actingAs($user);

        $response = $this->post(route('api.animeseries.store', $parameters));

        $response->assertCreated();
        static::assertDatabaseCount(AnimeSeries::TABLE, 1);
    }
}
