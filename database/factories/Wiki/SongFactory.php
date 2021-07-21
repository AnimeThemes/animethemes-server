<?php

declare(strict_types=1);

namespace Database\Factories\Wiki;

use App\Models\Wiki\Song;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * Class SongFactory.
 *
 * @method Song createOne($attributes = [])
 * @method Song makeOne($attributes = [])
 */
class SongFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Song::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition(): array
    {
        return [
            'title' => $this->faker->words(3, true),
        ];
    }
}
