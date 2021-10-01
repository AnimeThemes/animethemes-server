<?php

declare(strict_types=1);

namespace Database\Factories\Auth;

use App\Models\Auth\Team;
use App\Models\Auth\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * Class TeamFactory.
 *
 * @method Team createOne($attributes = [])
 * @method Team makeOne($attributes = [])
 */
class TeamFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Team::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition(): array
    {
        return [
            Team::ATTRIBUTE_NAME => Str::random(),
            Team::ATTRIBUTE_USER => User::factory(),
            Team::ATTRIBUTE_PERSONAL_TEAM => true,
        ];
    }
}
