<?php

declare(strict_types=1);

namespace Database\Factories\Wiki\Song;

use App\Models\Wiki\Artist;
use App\Models\Wiki\Song\Membership;
use Illuminate\Database\Eloquent\Factories\Attributes\UseModel;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @method Membership createOne($attributes = [])
 * @method Membership makeOne($attributes = [])
 *
 * @extends Factory<Membership>
 */
#[UseModel(Membership::class)]
class MembershipFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            Membership::ATTRIBUTE_ALIAS => fake()->text(),
            Membership::ATTRIBUTE_AS => fake()->text(),
            Membership::ATTRIBUTE_ARTIST => Artist::factory(),
            Membership::ATTRIBUTE_MEMBER => Artist::factory(),
        ];
    }
}
