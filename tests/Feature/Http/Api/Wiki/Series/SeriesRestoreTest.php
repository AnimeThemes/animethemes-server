<?php

declare(strict_types=1);

namespace Tests\Feature\Http\Api\Wiki\Series;

use App\Enums\Auth\ExtendedCrudPermission;
use App\Models\Auth\User;
use App\Models\Wiki\Series;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

/**
 * Class SeriesRestoreTest.
 */
class SeriesRestoreTest extends TestCase
{
    /**
     * The Series Restore Endpoint shall be protected by sanctum.
     *
     * @return void
     */
    public function test_protected(): void
    {
        $series = Series::factory()->trashed()->createOne();

        $response = $this->patch(route('api.series.restore', ['series' => $series]));

        $response->assertUnauthorized();
    }

    /**
     * The Series Restore Endpoint shall forbid users without the restore series permission.
     *
     * @return void
     */
    public function test_forbidden(): void
    {
        $series = Series::factory()->trashed()->createOne();

        $user = User::factory()->createOne();

        Sanctum::actingAs($user);

        $response = $this->patch(route('api.series.restore', ['series' => $series]));

        $response->assertForbidden();
    }

    /**
     * The Series Restore Endpoint shall forbid users from restoring a series that isn't trashed.
     *
     * @return void
     */
    public function test_trashed(): void
    {
        $series = Series::factory()->createOne();

        $user = User::factory()->withPermissions(ExtendedCrudPermission::RESTORE->format(Series::class))->createOne();

        Sanctum::actingAs($user);

        $response = $this->patch(route('api.series.restore', ['series' => $series]));

        $response->assertForbidden();
    }

    /**
     * The Series Restore Endpoint shall restore the series.
     *
     * @return void
     */
    public function test_restored(): void
    {
        $series = Series::factory()->trashed()->createOne();

        $user = User::factory()->withPermissions(ExtendedCrudPermission::RESTORE->format(Series::class))->createOne();

        Sanctum::actingAs($user);

        $response = $this->patch(route('api.series.restore', ['series' => $series]));

        $response->assertOk();
        static::assertNotSoftDeleted($series);
    }
}
