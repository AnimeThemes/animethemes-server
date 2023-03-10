<?php

declare(strict_types=1);

namespace Tests\Feature\Http\Api\Wiki\Series;

use App\Enums\Auth\ExtendedCrudPermission;
use App\Models\Auth\User;
use App\Models\Wiki\Series;
use Illuminate\Foundation\Testing\WithoutEvents;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

/**
 * Class SeriesForceDeleteTest.
 */
class SeriesForceDeleteTest extends TestCase
{
    use WithoutEvents;

    /**
     * The Series Force Delete Endpoint shall be protected by sanctum.
     *
     * @return void
     */
    public function testProtected(): void
    {
        $series = Series::factory()->createOne();

        $response = $this->delete(route('api.series.forceDelete', ['series' => $series]));

        $response->assertUnauthorized();
    }

    /**
     * The Series Force Delete Endpoint shall forbid users without the force delete series permission.
     *
     * @return void
     */
    public function testForbidden(): void
    {
        $series = Series::factory()->createOne();

        $user = User::factory()->createOne();

        Sanctum::actingAs($user);

        $response = $this->delete(route('api.series.forceDelete', ['series' => $series]));

        $response->assertForbidden();
    }

    /**
     * The Series Force Delete Endpoint shall force delete the series.
     *
     * @return void
     */
    public function testDeleted(): void
    {
        $series = Series::factory()->createOne();

        $user = User::factory()->withPermission(ExtendedCrudPermission::FORCE_DELETE()->format(Series::class))->createOne();

        Sanctum::actingAs($user);

        $response = $this->delete(route('api.series.forceDelete', ['series' => $series]));

        $response->assertOk();
        static::assertModelMissing($series);
    }
}
