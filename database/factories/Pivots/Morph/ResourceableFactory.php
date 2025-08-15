<?php

declare(strict_types=1);

namespace Database\Factories\Pivots\Morph;

use App\Models\Wiki\Anime;
use App\Models\Wiki\Artist;
use App\Models\Wiki\Song;
use App\Models\Wiki\Studio;
use App\Pivots\Morph\Resourceable;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * Class ResourceableFactory.
 *
 * @method Resourceable createOne($attributes = [])
 * @method Resourceable makeOne($attributes = [])
 *
 * @extends Factory<Resourceable>
 */
class ResourceableFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var class-string<Resourceable>
     */
    protected $model = Resourceable::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            Resourceable::ATTRIBUTE_AS => Str::random(),
        ];
    }

    /**
     * Resolve the resourceable for a given model.
     */
    public function forAnime(): static
    {
        return $this->for(Anime::factory(), Resourceable::RELATION_RESOURCEABLE);
    }

    /**
     * Resolve the resourceable for a given model.
     */
    public function forArtist(): static
    {
        return $this->for(Artist::factory(), Resourceable::RELATION_RESOURCEABLE);
    }

    /**
     * Resolve the resourceable for a given model.
     */
    public function forSong(): static
    {
        return $this->for(Song::factory(), Resourceable::RELATION_RESOURCEABLE);
    }

    /**
     * Resolve the resourceable for a given model.
     */
    public function forStudio(): static
    {
        return $this->for(Studio::factory(), Resourceable::RELATION_RESOURCEABLE);
    }
}
