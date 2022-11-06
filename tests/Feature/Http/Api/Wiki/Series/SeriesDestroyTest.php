<?php

declare(strict_types=1);

namespace Tests\Feature\Http\Api\Wiki\Series;

use App\Models\Auth\User;
use App\Models\Wiki\Series;
use Illuminate\Foundation\Testing\WithoutEvents;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

/**
 * Class SeriesDestroyTest.
 */
class SeriesDestroyTest extends TestCase
{
    use WithoutEvents;

    /**
     * The Series Destroy Endpoint shall be protected by sanctum.
     *
     * @return void
     */
    public function testProtected(): void
    {
        $series = Series::factory()->createOne();

        $response = $this->delete(route('api.series.destroy', ['series' => $series]));

        $response->assertUnauthorized();
    }

    /**
     * The Series Destroy Endpoint shall forbid users without the delete series permission.
     *
     * @return void
     */
    public function testForbidden(): void
    {
        $series = Series::factory()->createOne();

        $user = User::factory()->createOne();

        Sanctum::actingAs($user);

        $response = $this->delete(route('api.series.destroy', ['series' => $series]));

        $response->assertForbidden();
    }

    /**
     * The Series Destroy Endpoint shall forbid users from updating a series that is trashed.
     *
     * @return void
     */
    public function testTrashed(): void
    {
        $series = Series::factory()->createOne();

        $series->delete();

        $user = User::factory()->withPermission('delete series')->createOne();

        Sanctum::actingAs($user);

        $response = $this->delete(route('api.series.destroy', ['series' => $series]));

        $response->assertNotFound();
    }

    /**
     * The Series Destroy Endpoint shall delete the series.
     *
     * @return void
     */
    public function testDeleted(): void
    {
        $series = Series::factory()->createOne();

        $user = User::factory()->withPermission('delete series')->createOne();

        Sanctum::actingAs($user);

        $response = $this->delete(route('api.series.destroy', ['series' => $series]));

        $response->assertOk();
        static::assertSoftDeleted($series);
    }
}
