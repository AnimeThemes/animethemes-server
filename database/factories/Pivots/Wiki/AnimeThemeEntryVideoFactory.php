<?php

declare(strict_types=1);

namespace Database\Factories\Pivots\Wiki;

use App\Pivots\Wiki\AnimeThemeEntryVideo;
use Illuminate\Database\Eloquent\Factories\Attributes\UseModel;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @method AnimeThemeEntryVideo createOne($attributes = [])
 * @method AnimeThemeEntryVideo makeOne($attributes = [])
 *
 * @extends Factory<AnimeThemeEntryVideo>
 */
#[UseModel(AnimeThemeEntryVideo::class)]
class AnimeThemeEntryVideoFactory extends Factory
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
