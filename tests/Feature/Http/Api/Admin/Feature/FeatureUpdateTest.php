<?php

declare(strict_types=1);

namespace Tests\Feature\Http\Api\Admin\Feature;

use App\Enums\Auth\CrudPermission;
use App\Models\Admin\Feature;
use App\Models\Auth\User;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

/**
 * Class FeatureUpdateTest.
 */
class FeatureUpdateTest extends TestCase
{
    /**
     * The Feature Update Endpoint shall be protected by sanctum.
     *
     * @return void
     */
    public function testProtected(): void
    {
        $feature = Feature::factory()->createOne();

        $parameters = [
            Feature::ATTRIBUTE_VALUE => ! $feature->value,
        ];

        $response = $this->put(route('api.feature.update', ['feature' => $feature] + $parameters));

        $response->assertUnauthorized();
    }

    /**
     * The Feature Update Endpoint shall forbid users without the update feature permission.
     *
     * @return void
     */
    public function testForbidden(): void
    {
        $feature = Feature::factory()->createOne();

        $parameters = [
            Feature::ATTRIBUTE_VALUE => ! $feature->value,
        ];

        $user = User::factory()->createOne();

        Sanctum::actingAs($user);

        $response = $this->put(route('api.feature.update', ['feature' => $feature] + $parameters));

        $response->assertForbidden();
    }

    /**
     * The Feature Update Endpoint shall update a feature.
     *
     * @return void
     */
    public function testUpdate(): void
    {
        $feature = Feature::factory()->createOne();

        $parameters = [
            Feature::ATTRIBUTE_VALUE => ! $feature->value,
        ];

        $user = User::factory()->withPermissions(CrudPermission::UPDATE()->format(Feature::class))->createOne();

        Sanctum::actingAs($user);

        $response = $this->put(route('api.feature.update', ['feature' => $feature] + $parameters));

        $response->assertOk();
    }
}
