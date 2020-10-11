<?php

namespace Database\Factories;

use App\Models\Synonym;
use Illuminate\Database\Eloquent\Factories\Factory;

class SynonymFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Synonym::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'text' => $this->faker->words(3, true),
        ];
    }
}
