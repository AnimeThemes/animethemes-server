<?php

declare(strict_types=1);

namespace Database\Factories\User\Submission;

use App\Models\User\Submission;
use App\Models\User\Submission\SubmissionStage;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @method SubmissionStage createOne($attributes = [])
 * @method SubmissionStage makeOne($attributes = [])
 *
 * @extends Factory<SubmissionStage>
 */
class SubmissionStageFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var class-string<SubmissionStage>
     */
    protected $model = SubmissionStage::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            SubmissionStage::ATTRIBUTE_SUBMISSION => Submission::factory(),
            SubmissionStage::ATTRIBUTE_MODERATOR_NOTES => fake()->text(),
        ];
    }
}
