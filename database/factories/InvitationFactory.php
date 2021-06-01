<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Enums\InvitationStatus;
use App\Models\Invitation;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * Class InvitationFactory
 * @package Database\Factories
 */
class InvitationFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Invitation::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->name,
            'email' => $this->faker->unique()->safeEmail,
            'status' => InvitationStatus::OPEN,
        ];
    }
}
