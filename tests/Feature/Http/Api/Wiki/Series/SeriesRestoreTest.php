<?php

declare(strict_types=1);

namespace Tests\Feature\Http\Api\Wiki\Series;

use App\Models\Auth\User;
use App\Models\Wiki\Series;
use Illuminate\Foundation\Testing\WithoutEvents;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

/**
 * Class SeriesRestoreTest.
 */
class SeriesRestoreTest extends TestCase
{
    use WithoutEvents;

    /**
     * The Series Restore Endpoint shall be protected by sanctum.
     *
     * @return void
     */
    public function testProtected(): void
    {
        $series = Series::factory()->createOne();

        $series->delete();

        $response = $this->patch(route('api.series.restore', ['series' => $series]));

        $response->assertUnauthorized();
    }

    /**
     * The Series Restore Endpoint shall forbid users without the restore series permission.
     *
     * @return void
     */
    public function testForbidden(): void
    {
        $series = Series::factory()->createOne();

        $series->delete();

        $user = User::factory()->createOne();

        Sanctum::actingAs($user);

        $response = $this->patch(route('api.series.restore', ['series' => $series]));

        $response->assertForbidden();
    }

    /**
     * The Series Restore Endpoint shall restore the series.
     *
     * @return void
     */
    public function testRestored(): void
    {
        $series = Series::factory()->createOne();

        $series->delete();

        $user = User::factory()->withPermission('restore series')->createOne();

        Sanctum::actingAs($user);

        $response = $this->patch(route('api.series.restore', ['series' => $series]));

        $response->assertOk();
        static::assertNotSoftDeleted($series);
    }
}
