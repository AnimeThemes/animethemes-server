<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Enums\ResourceSite;
use App\Models\ExternalResource;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * Class ExternalResourceFactory
 * @package Database\Factories
 */
class ExternalResourceFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = ExternalResource::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition(): array
    {
        return [
            'site' => ResourceSite::getRandomValue(),
            'link' => $this->faker->url,
            'external_id' => $this->faker->randomNumber(),
        ];
    }
}
