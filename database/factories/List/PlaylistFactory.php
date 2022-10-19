<?php

declare(strict_types=1);

namespace Database\Factories\List;

use App\Enums\Models\List\PlaylistVisibility;
use App\Models\List\Playlist;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * Class PlaylistFactory.
 *
 * @method Playlist createOne($attributes = [])
 * @method Playlist makeOne($attributes = [])
 *
 * @extends Factory<Playlist>
 */
class PlaylistFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var class-string<Playlist>
     */
    protected $model = Playlist::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition(): array
    {
        return [
            Playlist::ATTRIBUTE_NAME => fake()->words(3, true),
            Playlist::ATTRIBUTE_VISIBILITY => PlaylistVisibility::getRandomValue(),
        ];
    }
}
