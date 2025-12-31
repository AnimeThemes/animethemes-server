<?php

declare(strict_types=1);

namespace Database\Factories\Admin;

use App\Enums\Http\Api\Filter\AllowedDateFormat;
use App\Models\Admin\Announcement;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @method Announcement createOne($attributes = [])
 * @method Announcement makeOne($attributes = [])
 *
 * @extends Factory<Announcement>
 */
class AnnouncementFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var class-string<Announcement>
     */
    protected $model = Announcement::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            Announcement::ATTRIBUTE_CONTENT => fake()->sentence(),
            Announcement::ATTRIBUTE_END_AT => fake()->dateTimeBetween('+1 day', '+1 year')->format(AllowedDateFormat::YMDHISU->value),
            Announcement::ATTRIBUTE_START_AT => fake()->dateTimeBetween()->format(AllowedDateFormat::YMDHISU->value),
        ];
    }

    /**
     * Set the announcement time to past.
     */
    public function past(): static
    {
        return $this->state([
            Announcement::ATTRIBUTE_END_AT => fake()->dateTimeBetween('-2 years', '-1 year')->format(AllowedDateFormat::YMDHISU->value),
            Announcement::ATTRIBUTE_START_AT => fake()->dateTimeBetween('-3 years', '-2 years')->format(AllowedDateFormat::YMDHISU->value),
        ]);
    }

    /**
     * Set the announcement time to future.
     */
    public function future(): static
    {
        return $this->state([
            Announcement::ATTRIBUTE_END_AT => fake()->dateTimeBetween('+1 year', '+2 years')->format(AllowedDateFormat::YMDHISU->value),
            Announcement::ATTRIBUTE_START_AT => fake()->dateTimeBetween('+1 day', '+1 year')->format(AllowedDateFormat::YMDHISU->value),
        ]);
    }
}
