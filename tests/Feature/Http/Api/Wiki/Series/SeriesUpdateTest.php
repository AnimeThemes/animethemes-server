<?php

declare(strict_types=1);

namespace Tests\Feature\Http\Api\Wiki\Series;

use App\Enums\Auth\CrudPermission;
use App\Models\Auth\User;
use App\Models\Wiki\Series;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

/**
 * Class SeriesUpdateTest.
 */
class SeriesUpdateTest extends TestCase
{
    /**
     * The Series Update Endpoint shall be protected by sanctum.
     *
     * @return void
     */
    public function testProtected(): void
    {
        $series = Series::factory()->createOne();

        $parameters = Series::factory()->raw();

        $response = $this->put(route('api.series.update', ['series' => $series] + $parameters));

        $response->assertUnauthorized();
    }

    /**
     * The Series Update Endpoint shall forbid users without the update series permission.
     *
     * @return void
     */
    public function testForbidden(): void
    {
        $series = Series::factory()->createOne();

        $parameters = Series::factory()->raw();

        $user = User::factory()->createOne();

        Sanctum::actingAs($user);

        $response = $this->put(route('api.series.update', ['series' => $series] + $parameters));

        $response->assertForbidden();
    }

    /**
     * The Series Update Endpoint shall forbid users from updating a series that is trashed.
     *
     * @return void
     */
    public function testTrashed(): void
    {
        $series = Series::factory()->trashed()->createOne();

        $parameters = Series::factory()->raw();

        $user = User::factory()->withPermissions(CrudPermission::UPDATE->format(Series::class))->createOne();

        Sanctum::actingAs($user);

        $response = $this->put(route('api.series.update', ['series' => $series] + $parameters));

        $response->assertForbidden();
    }

    /**
     * The Series Update Endpoint shall update a series.
     *
     * @return void
     */
    public function testUpdate(): void
    {
        $series = Series::factory()->createOne();

        $parameters = Series::factory()->raw();

        $user = User::factory()->withPermissions(CrudPermission::UPDATE->format(Series::class))->createOne();

        Sanctum::actingAs($user);

        $response = $this->put(route('api.series.update', ['series' => $series] + $parameters));

        $response->assertOk();
    }
}
