<?php

declare(strict_types=1);

namespace Database\Factories\Auth;

use App\Enums\Models\Auth\InvitationStatus;
use App\Models\Auth\Invitation;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * Class InvitationFactory.
 *
 * @method Invitation createOne($attributes = [])
 * @method Invitation makeOne($attributes = [])
 *
 * @extends Factory<Invitation>
 */
class InvitationFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var class-string<Invitation>
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
            Invitation::ATTRIBUTE_NAME => $this->faker->name(),
            Invitation::ATTRIBUTE_EMAIL => $this->faker->safeEmail(),
            Invitation::ATTRIBUTE_STATUS => InvitationStatus::OPEN,
        ];
    }
}
