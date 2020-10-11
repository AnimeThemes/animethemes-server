<?php

namespace Database\Factories;

use App\Enums\ResourceType;
use App\Models\ExternalResource;
use Illuminate\Database\Eloquent\Factories\Factory;

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
    public function definition()
    {
        return [
            'type' => ResourceType::getRandomValue(),
            'link' => $this->faker->url,
            'external_id' => $this->faker->randomNumber(),
        ];
    }
}
