<?php

declare(strict_types=1);

namespace Database\Factories\List\External;

use App\Enums\Models\List\ExternalEntryWatchStatus;
use App\Models\List\External\ExternalEntry;
use Illuminate\Database\Eloquent\Factories\Attributes\UseModel;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Arr;

/**
 * @method ExternalEntry createOne($attributes = [])
 * @method ExternalEntry makeOne($attributes = [])
 *
 * @extends Factory<ExternalEntry>
 */
#[UseModel(ExternalEntry::class)]
class ExternalEntryFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $watchStatus = Arr::random(ExternalEntryWatchStatus::cases());

        return [
            ExternalEntry::ATTRIBUTE_WATCH_STATUS => $watchStatus->value,
            ExternalEntry::ATTRIBUTE_SCORE => fake()->randomFloat(2),
            ExternalEntry::ATTRIBUTE_IS_FAVORITE => fake()->boolean(),
        ];
    }
}
