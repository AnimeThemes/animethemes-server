<?php

declare(strict_types=1);

namespace Database\Factories\Pivots\Wiki;

use App\Pivots\Wiki\EntryVideo;
use Illuminate\Database\Eloquent\Factories\Attributes\UseModel;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @method EntryVideo createOne($attributes = [])
 * @method EntryVideo makeOne($attributes = [])
 *
 * @extends Factory<EntryVideo>
 */
#[UseModel(EntryVideo::class)]
class EntryVideoFactory extends Factory
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
