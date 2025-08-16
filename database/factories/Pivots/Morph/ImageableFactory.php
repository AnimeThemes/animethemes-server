<?php

declare(strict_types=1);

namespace Database\Factories\Pivots\Morph;

use App\Models\Auth\User;
use App\Models\List\Playlist;
use App\Models\Wiki\Anime;
use App\Models\Wiki\Artist;
use App\Models\Wiki\Studio;
use App\Pivots\Morph\Imageable;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * Class ImageableFactory.
 *
 * @method Imageable createOne($attributes = [])
 * @method Imageable makeOne($attributes = [])
 *
 * @extends Factory<Imageable>
 */
class ImageableFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var class-string<Imageable>
     */
    protected $model = Imageable::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            Imageable::ATTRIBUTE_DEPTH => fake()->randomDigitNotNull(),
        ];
    }

    /**
     * Resolve the imageable for a given model.
     */
    public function forPlaylist(): static
    {
        return $this->for(Playlist::factory()->for(User::factory()), Imageable::RELATION_IMAGEABLE);
    }

    /**
     * Resolve the imageable for a given model.
     */
    public function forAnime(): static
    {
        return $this->for(Anime::factory(), Imageable::RELATION_IMAGEABLE);
    }

    /**
     * Resolve the imageable for a given model.
     */
    public function forArtist(): static
    {
        return $this->for(Artist::factory(), Imageable::RELATION_IMAGEABLE);
    }

    /**
     * Resolve the imageable for a given model.
     */
    public function forStudio(): static
    {
        return $this->for(Studio::factory(), Imageable::RELATION_IMAGEABLE);
    }
}
