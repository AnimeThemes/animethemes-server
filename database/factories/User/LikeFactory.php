<?php

declare(strict_types=1);

namespace Database\Factories\User;

use App\Models\Auth\User;
use App\Models\List\Playlist;
use App\Models\User\Like;
use App\Models\Wiki\Anime\Theme\AnimeThemeEntry;
use Illuminate\Database\Eloquent\Factories\Attributes\UseModel;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @method Like createOne($attributes = [])
 * @method Like makeOne($attributes = [])
 *
 * @extends Factory<Like>
 */
#[UseModel(Like::class)]
class LikeFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            Like::ATTRIBUTE_USER => User::factory(),
        ];
    }

    public function forPlaylist(): static
    {
        return $this->for(Playlist::factory(), Like::RELATION_LIKEABLE);
    }

    public function forEntry(): static
    {
        return $this->for(AnimeThemeEntry::factory(), Like::RELATION_LIKEABLE);
    }
}
