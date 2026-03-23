<?php

declare(strict_types=1);

namespace Database\Factories\Wiki;

use App\Models\Wiki\Song;
use Illuminate\Database\Eloquent\Factories\Attributes\UseModel;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @method Song createOne($attributes = [])
 * @method Song makeOne($attributes = [])
 *
 * @extends Factory<Song>
 */
#[UseModel(Song::class)]
class SongFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            Song::ATTRIBUTE_TITLE => fake()->words(3, true),
            Song::ATTRIBUTE_TITLE_NATIVE => fake()->words(3, true),
        ];
    }
}
