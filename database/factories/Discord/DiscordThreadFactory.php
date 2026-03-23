<?php

declare(strict_types=1);

namespace Database\Factories\Discord;

use App\Models\Discord\DiscordThread;
use Illuminate\Database\Eloquent\Factories\Attributes\UseModel;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @method DiscordThread createOne($attributes = [])
 * @method DiscordThread makeOne($attributes = [])
 *
 * @extends Factory<DiscordThread>
 */
#[UseModel(DiscordThread::class)]
class DiscordThreadFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            DiscordThread::ATTRIBUTE_NAME => fake()->words(3, true),
            DiscordThread::ATTRIBUTE_ID => fake()->words(3, true),
        ];
    }
}
