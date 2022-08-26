<?php

declare(strict_types=1);

namespace Database\Factories\Admin;

use App\Models\Admin\Setting;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * Class SettingFactory.
 *
 * @method Setting createOne($attributes = [])
 * @method Setting makeOne($attributes = [])
 *
 * @extends Factory<Setting>
 */
class SettingFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var class-string<Setting>
     */
    protected $model = Setting::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition(): array
    {
        return [
            Setting::ATTRIBUTE_KEY => fake()->slug(),
            Setting::ATTRIBUTE_VALUE => fake()->word(),
        ];
    }
}
