<?php

declare(strict_types=1);

namespace Database\Factories\User\Submission;

use App\Enums\Models\User\ApprovableStatus;
use App\Enums\Models\User\SubmissionActionType;
use App\Models\User\Submission\SubmissionStep;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Arr;

/**
 * @method SubmissionStep createOne($attributes = [])
 * @method SubmissionStep makeOne($attributes = [])
 *
 * @extends Factory<SubmissionStep>
 */
class SubmissionStepFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var class-string<SubmissionStep>
     */
    protected $model = SubmissionStep::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $action = Arr::random(SubmissionActionType::cases());
        $status = Arr::random(ApprovableStatus::cases());

        return [
            SubmissionStep::ATTRIBUTE_ACTION => $action->value,
            SubmissionStep::ATTRIBUTE_STATUS => $status->value,
        ];
    }
}
