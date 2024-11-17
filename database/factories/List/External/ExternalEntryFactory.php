<?php

declare(strict_types=1);

namespace Database\Factories\List\External;

use App\Enums\Models\List\ExternalEntryWatchStatus;
use App\Models\List\External\ExternalEntry;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Arr;

/**
 * Class ExternalEntryFactory.
 *
 * @method ExternalEntry createOne($attributes = [])
 * @method ExternalEntry makeOne($attributes = [])
 *
 * @extends Factory<ExternalEntry>
 */
class ExternalEntryFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var class-string<ExternalEntry>
     */
    protected $model = ExternalEntry::class;

    /**
     * Define the model's default state.
     *
     * @phpstan-ignore-next-line
     * @return array
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
