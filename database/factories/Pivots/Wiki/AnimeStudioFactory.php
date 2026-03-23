<?php

declare(strict_types=1);

namespace Database\Factories\Pivots\Wiki;

use App\Pivots\Wiki\AnimeStudio;
use Illuminate\Database\Eloquent\Factories\Attributes\UseModel;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @method AnimeStudio createOne($attributes = [])
 * @method AnimeStudio makeOne($attributes = [])
 *
 * @extends Factory<AnimeStudio>
 */
#[UseModel(AnimeStudio::class)]
class AnimeStudioFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            //
        ];
    }
}
