<?php declare(strict_types=1);

namespace Database\Factories;

use App\Models\Entry;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * Class EntryFactory
 * @package Database\Factories
 */
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
    public function definition(): array
    {
        return [
            'version' => $this->faker->randomDigitNotNull,
            'episodes' => $this->faker->word,
            'nsfw' => $this->faker->boolean,
            'spoiler' => $this->faker->boolean,
            'notes' => $this->faker->word,
        ];
    }
}
