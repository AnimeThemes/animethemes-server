<?php

declare(strict_types=1);

namespace Database\Factories\Wiki;

use App\Models\Wiki\Studio;
use Illuminate\Database\Eloquent\Factories\Attributes\UseModel;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @method Studio createOne($attributes = [])
 * @method Studio makeOne($attributes = [])
 *
 * @extends Factory<Studio>
 */
#[UseModel(Studio::class)]
class StudioFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            Studio::ATTRIBUTE_NAME => fake()->words(3, true),
            Studio::ATTRIBUTE_SLUG => Str::slug(fake()->text(191), '_'),
        ];
    }
}
