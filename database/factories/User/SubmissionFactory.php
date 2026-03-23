<?php

declare(strict_types=1);

namespace Database\Factories\User;

use App\Enums\Models\User\SubmissionStatus;
use App\Models\User\Submission;
use Illuminate\Database\Eloquent\Factories\Attributes\UseModel;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Arr;

/**
 * @method Submission createOne($attributes = [])
 * @method Submission makeOne($attributes = [])
 *
 * @extends Factory<Submission>
 */
#[UseModel(Submission::class)]
class SubmissionFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $status = Arr::random(SubmissionStatus::cases());

        return [
            Submission::ATTRIBUTE_MODERATOR_NOTES => fake()->text(),
            Submission::ATTRIBUTE_STATUS => $status->value,
            Submission::ATTRIBUTE_TYPE => fake()->text(),
        ];
    }
}
