<?php

declare(strict_types=1);

namespace Database\Factories\List;

use App\Enums\Models\List\PlaylistVisibility;
use App\Models\Auth\User;
use App\Models\List\Playlist;
use Illuminate\Database\Eloquent\Factories\Attributes\UseModel;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Arr;

/**
 * @method Playlist createOne($attributes = [])
 * @method Playlist makeOne($attributes = [])
 *
 * @extends Factory<Playlist>
 */
#[UseModel(Playlist::class)]
class PlaylistFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $visibility = Arr::random(PlaylistVisibility::cases());

        return [
            Playlist::ATTRIBUTE_NAME => fake()->words(3, true),
            Playlist::ATTRIBUTE_DESCRIPTION => fake()->words(10, true),
            Playlist::ATTRIBUTE_VISIBILITY => $visibility->value,
            Playlist::ATTRIBUTE_USER => User::factory(),
        ];
    }
}
