<?php

declare(strict_types=1);

namespace Database\Factories\Admin;

use App\Enums\Models\Admin\ApprovableStatus;
use App\Models\Admin\Report;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Arr;

/**
 * Class ReportFactory.
 *
 * @method Report createOne($attributes = [])
 * @method Report makeOne($attributes = [])
 *
 * @extends Factory<Report>
 */
class ReportFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var class-string<Report>
     */
    protected $model = Report::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $status = Arr::random(ApprovableStatus::cases());

        return [
            Report::ATTRIBUTE_MOD_NOTES => fake()->text(),
            Report::ATTRIBUTE_STATUS => $status->value,
        ];
    }
}
