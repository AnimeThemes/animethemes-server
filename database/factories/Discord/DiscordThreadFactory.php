<?php

declare(strict_types=1);

namespace Database\Factories\Discord;

use App\Models\Discord\DiscordThread;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * Class DiscordThreadFactory.
 *
 * @method DiscordThread createOne($attributes = [])
 * @method DiscordThread makeOne($attributes = [])
 *
 * @extends Factory<DiscordThread>
 */
class DiscordThreadFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var class-string<DiscordThread>
     */
    protected $model = DiscordThread::class;

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
