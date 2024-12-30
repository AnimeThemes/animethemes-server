<?php

declare(strict_types=1);

namespace Database\Factories\Admin\Report;

use App\Enums\Models\Admin\ApprovableStatus;
use App\Enums\Models\Admin\ReportActionType;
use App\Models\Admin\Report\ReportStep;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Arr;

/**
 * Class ReportStepFactory.
 *
 * @method ReportStep createOne($attributes = [])
 * @method ReportStep makeOne($attributes = [])
 *
 * @extends Factory<ReportStep>
 */
class ReportStepFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var class-string<ReportStep>
     */
    protected $model = ReportStep::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $action = Arr::random(ReportActionType::cases());
        $status = Arr::random(ApprovableStatus::cases());

        return [
            ReportStep::ATTRIBUTE_ACTION => $action->value,
            ReportStep::ATTRIBUTE_STATUS => $status->value,
        ];
    }
}
