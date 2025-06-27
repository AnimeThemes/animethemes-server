<?php

declare(strict_types=1);

namespace Tests\Feature\Http\Api\Admin\Feature;

use App\Http\Api\Field\Field;
use App\Http\Api\Parser\FieldParser;
use App\Http\Api\Query\Query;
use App\Http\Api\Schema\Admin\FeatureSchema;
use App\Http\Resources\Admin\Resource\FeatureResource;
use App\Models\Admin\Feature;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

/**
 * Class FeatureShowTest.
 */
class FeatureShowTest extends TestCase
{
    use WithFaker;

    /**
     * By default, the Feature Show Endpoint shall return a Feature Resource.
     *
     * @return void
     */
    public function test_default(): void
    {
        $feature = Feature::factory()->create();

        $response = $this->get(route('api.feature.show', ['feature' => $feature]));

        $response->assertJson(
            json_decode(
                json_encode(
                    new FeatureResource($feature, new Query())
                        ->response()
                        ->getData()
                ),
                true
            )
        );
    }

    /**
     * The Feature Show Endpoint shall forbid showing features of nonnull scope.
     *
     * @return void
     */
    public function test_non_null_forbidden(): void
    {
        $feature = Feature::factory()->create([
            Feature::ATTRIBUTE_SCOPE => $this->faker->word(),
        ]);

        $response = $this->get(route('api.feature.show', ['feature' => $feature]));

        $response->assertForbidden();
    }

    /**
     * The Feature Show Endpoint shall implement sparse fieldsets.
     *
     * @return void
     */
    public function test_sparse_fieldsets(): void
    {
        $schema = new FeatureSchema();

        $fields = collect($schema->fields());

        $includedFields = $fields->random($this->faker->numberBetween(1, $fields->count()));

        $parameters = [
            FieldParser::param() => [
                FeatureResource::$wrap => $includedFields->map(fn (Field $field) => $field->getKey())->join(','),
            ],
        ];

        $feature = Feature::factory()->create();

        $response = $this->get(route('api.feature.show', ['feature' => $feature] + $parameters));

        $response->assertJson(
            json_decode(
                json_encode(
                    new FeatureResource($feature, new Query($parameters))
                        ->response()
                        ->getData()
                ),
                true
            )
        );
    }
}
