<?php

declare(strict_types=1);

namespace Database\Factories\Wiki;

use App\Models\Wiki\Artist;
use App\Models\Wiki\Theme;
use App\Models\Wiki\ThemeStaff;
use Illuminate\Database\Eloquent\Factories\Attributes\UseModel;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @method ThemeStaff createOne($attributes = [])
 * @method ThemeStaff makeOne($attributes = [])
 *
 * @extends Factory<ThemeStaff>
 */
#[UseModel(ThemeStaff::class)]
class ThemeStaffFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            ThemeStaff::ATTRIBUTE_ARTIST => Artist::factory(),
            ThemeStaff::ATTRIBUTE_THEME => Theme::factory(),
            ThemeStaff::ATTRIBUTE_ROLE => fake()->word(),
        ];
    }
}
