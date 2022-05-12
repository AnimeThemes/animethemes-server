<?php

declare(strict_types=1);

namespace Tests\Feature\Http\Api\Wiki\Series;

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
     * The Series Force Destroy Endpoint shall be protected by sanctum.
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
     * The Series Force Destroy Endpoint shall force delete the series.
     *
     * @return void
     */
    public function testDeleted(): void
    {
        $series = Series::factory()->createOne();

        $user = User::factory()->createOne();

        $user->givePermissionTo('force delete series');

        Sanctum::actingAs($user);

        $response = $this->delete(route('api.series.forceDelete', ['series' => $series]));

        $response->assertOk();
        static::assertModelMissing($series);
    }
}
