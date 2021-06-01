<?php declare(strict_types=1);

namespace Database\Factories;

use App\Models\Series;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * Class SeriesFactory
 * @package Database\Factories
 */
class SeriesFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Series::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition(): array
    {
        return [
            'slug' => Str::slug($this->faker->words(3, true), '_'),
            'name' => $this->faker->words(3, true),
        ];
    }
}
