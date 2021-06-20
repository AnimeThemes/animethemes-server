<?php

declare(strict_types=1);

namespace Database\Factories\Auth;

use App\Models\Auth\Team;
use App\Models\Auth\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * Class TeamFactory.
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
            'name' => Str::random(),
            'user_id' => User::factory(),
            'personal_team' => true,
        ];
    }
}
