<?php

declare(strict_types=1);

namespace Database\Factories\Wiki\Song;

use App\Models\Wiki\Artist;
use App\Models\Wiki\Song\Membership;
use App\Models\Wiki\Song\Performance;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Relations\Relation;

/**
 * Class PerformanceFactory.
 *
 * @method Performance createOne($attributes = [])
 * @method Performance makeOne($attributes = [])
 *
 * @extends Factory<Performance>
 */
class PerformanceFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var class-string<Performance>
     */
    protected $model = Performance::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            Performance::ATTRIBUTE_ALIAS => fake()->text(),
            Performance::ATTRIBUTE_AS => fake()->text(),
        ];
    }

    /**
     * Set the artist.
     */
    public function artist(Artist|Membership $artist): static
    {
        return $this->state([
            Performance::ATTRIBUTE_ARTIST_TYPE => Relation::getMorphAlias($artist->getMorphClass()),
            Performance::ATTRIBUTE_ARTIST_ID => $artist->getKey(),
        ]);
    }
}
