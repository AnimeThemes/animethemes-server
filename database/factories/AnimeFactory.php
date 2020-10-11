<?php

namespace Database\Factories;

use App\Enums\Season;
use App\Models\Anime;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class AnimeFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Anime::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'alias' => Str::slug($this->faker->words(3, true), '_'),
            'name' => $this->faker->words(3, true),
            'year' => $this->faker->year,
            'season' => Season::getRandomValue(),
            'synopsis' => $this->faker->text,
            'cover' => Str::random(40).'.png',
        ];
    }
}
