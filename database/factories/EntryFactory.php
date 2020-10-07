<?php

namespace Database\Factories;

use App\Models\Entry;
use Illuminate\Database\Eloquent\Factories\Factory;

class EntryFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Entry::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'version' => $this->faker->randomDigitNotNull,
            'episodes' => $this->faker->word,
            'nsfw' => $this->faker->boolean,
            'spoiler' => $this->faker->boolean,
            'notes' => $this->faker->word
        ];
    }
}
