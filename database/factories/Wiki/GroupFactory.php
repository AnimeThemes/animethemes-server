<?php

declare(strict_types=1);

namespace Database\Factories\Wiki;

use App\Models\Wiki\Group;
use Illuminate\Database\Eloquent\Factories\Attributes\UseModel;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @method Group createOne($attributes = [])
 * @method Group makeOne($attributes = [])
 *
 * @extends Factory<Group>
 */
#[UseModel(Group::class)]
class GroupFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            Group::ATTRIBUTE_NAME => fake()->words(3, true),
            Group::ATTRIBUTE_SLUG => Str::slug(fake()->text(191), '_'),
        ];
    }
}
