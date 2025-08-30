<?php

declare(strict_types=1);

namespace Database\Factories\Wiki;

use App\Models\Wiki\Song;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @method Song createOne($attributes = [])
 * @method Song makeOne($attributes = [])
 *
 * @extends Factory<Song>
 */
class SongFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var class-string<Song>
     */
    protected $model = Song::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            Song::ATTRIBUTE_TITLE => fake()->words(3, true),
        ];
    }
}
