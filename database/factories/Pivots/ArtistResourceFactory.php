<?php

namespace Database\Factories\Pivots;

use Illuminate\Database\Eloquent\Factories\Factory;

class ArtistResourceFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = \App\Pivots\ArtistResource::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'as' => $this->faker->word(),
        ];
    }
}
