<?php

declare(strict_types=1);

namespace Database\Factories\Auth;

use App\Enums\Models\Auth\InvitationStatus;
use App\Models\Auth\Invitation;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * Class InvitationFactory.
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
            'name' => $this->faker->name(),
            'email' => $this->faker->safeEmail(),
            'status' => InvitationStatus::OPEN,
        ];
    }
}
