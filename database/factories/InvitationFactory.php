<?php

namespace Database\Factories;

use App\Enums\InvitationStatus;
use App\Enums\UserRole;
use App\Models\Invitation;
use Illuminate\Database\Eloquent\Factories\Factory;

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
    public function definition()
    {
        return [
            'token' => Invitation::createToken(),
            'name' => $this->faker->name,
            'email' => $this->faker->unique()->safeEmail,
            'role' => UserRole::READ_ONLY,
            'status' => InvitationStatus::OPEN,
        ];
    }
}
