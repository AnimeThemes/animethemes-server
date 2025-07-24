<?php

declare(strict_types=1);

namespace Tests\Feature\Http\Api\Wiki\Series;

use App\Enums\Auth\CrudPermission;
use App\Models\Auth\User;
use App\Models\Wiki\Series;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class SeriesDestroyTest extends TestCase
{
    /**
     * The Series Destroy Endpoint shall be protected by sanctum.
     */
    public function testProtected(): void
    {
        $series = Series::factory()->createOne();

        $response = $this->delete(route('api.series.destroy', ['series' => $series]));

        $response->assertUnauthorized();
    }

    /**
     * The Series Destroy Endpoint shall forbid users without the delete series permission.
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
     */
    public function testTrashed(): void
    {
        $series = Series::factory()->trashed()->createOne();

        $user = User::factory()->withPermissions(CrudPermission::DELETE->format(Series::class))->createOne();

        Sanctum::actingAs($user);

        $response = $this->delete(route('api.series.destroy', ['series' => $series]));

        $response->assertNotFound();
    }

    /**
     * The Series Destroy Endpoint shall delete the series.
     */
    public function testDeleted(): void
    {
        $series = Series::factory()->createOne();

        $user = User::factory()->withPermissions(CrudPermission::DELETE->format(Series::class))->createOne();

        Sanctum::actingAs($user);

        $response = $this->delete(route('api.series.destroy', ['series' => $series]));

        $response->assertOk();
        static::assertSoftDeleted($series);
    }
}
