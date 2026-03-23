<?php

declare(strict_types=1);

namespace Database\Factories\Wiki;

use App\Models\Wiki\Audio;
use Illuminate\Database\Eloquent\Factories\Attributes\UseModel;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @method Audio createOne($attributes = [])
 * @method Audio makeOne($attributes = [])
 *
 * @extends Factory<Audio>
 */
#[UseModel(Audio::class)]
class AudioFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            Audio::ATTRIBUTE_BASENAME => Str::random(),
            Audio::ATTRIBUTE_FILENAME => Str::random(),
            Audio::ATTRIBUTE_MIMETYPE => fake()->mimeType(),
            Audio::ATTRIBUTE_PATH => Str::random(),
            Audio::ATTRIBUTE_SIZE => fake()->randomDigitNotZero(),
        ];
    }
}
