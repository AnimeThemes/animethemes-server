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
 * Class AnimeSeriesDestroyTest.
 */
class AnimeSeriesDestroyTest extends TestCase
{
    /**
     * The Anime Series Destroy Endpoint shall be protected by sanctum.
     *
     * @return void
     */
    public function testProtected(): void
    {
        $animeSeries = AnimeSeries::factory()
            ->for(Anime::factory())
            ->for(Series::factory())
            ->createOne();

        $response = $this->delete(route('api.animeseries.destroy', ['anime' => $animeSeries->anime, 'series' => $animeSeries->series]));

        $response->assertUnauthorized();
    }

    /**
     * The Anime Series Destroy Endpoint shall forbid users without the delete anime & delete series permissions.
     *
     * @return void
     */
    public function testForbidden(): void
    {
        $animeSeries = AnimeSeries::factory()
            ->for(Anime::factory())
            ->for(Series::factory())
            ->createOne();

        $user = User::factory()->createOne();

        Sanctum::actingAs($user);

        $response = $this->delete(route('api.animeseries.destroy', ['anime' => $animeSeries->anime, 'series' => $animeSeries->series]));

        $response->assertForbidden();
    }

    /**
     * The Anime Series Destroy Endpoint shall return an error if the anime series does not exist.
     *
     * @return void
     */
    public function testNotFound(): void
    {
        $anime = Anime::factory()->createOne();
        $series = Series::factory()->createOne();

        $user = User::factory()
            ->withPermissions(
                CrudPermission::DELETE()->format(Anime::class),
                CrudPermission::DELETE()->format(Series::class)
            )
            ->createOne();

        Sanctum::actingAs($user);

        $response = $this->delete(route('api.animeseries.destroy', ['anime' => $anime, 'series' => $series]));

        $response->assertNotFound();
    }

    /**
     * The Anime Series Destroy Endpoint shall delete the anime series.
     *
     * @return void
     */
    public function testDeleted(): void
    {
        $animeSeries = AnimeSeries::factory()
            ->for(Anime::factory())
            ->for(Series::factory())
            ->createOne();

        $user = User::factory()
            ->withPermissions(
                CrudPermission::DELETE()->format(Anime::class),
                CrudPermission::DELETE()->format(Series::class)
            )
            ->createOne();

        Sanctum::actingAs($user);

        $response = $this->delete(route('api.animeseries.destroy', ['anime' => $animeSeries->anime, 'series' => $animeSeries->series]));

        $response->assertOk();
        static::assertModelMissing($animeSeries);
    }
}
