<?php

declare(strict_types=1);

namespace Database\Factories\User;

use App\Enums\Models\User\SubmissionStatus;
use App\Models\User\Submission;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Arr;

/**
 * @method Submission createOne($attributes = [])
 * @method Submission makeOne($attributes = [])
 *
 * @extends Factory<Submission>
 */
class SubmissionFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var class-string<Submission>
     */
    protected $model = Submission::class;

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
