<?php

declare(strict_types=1);

namespace Database\Factories\Auth;

use App\Models\Auth\Team;
use App\Models\Auth\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use Laravel\Jetstream\Features;

/**
 * Class UserFactory.
 *
 * @method User createOne($attributes = [])
 * @method User makeOne($attributes = [])
 *
 * @extends Factory<User>
 */
class UserFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var class-string<User>
     */
    protected $model = User::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition(): array
    {
        return [
            User::ATTRIBUTE_NAME => $this->faker->name(),
            User::ATTRIBUTE_EMAIL => $this->faker->safeEmail(),
            User::ATTRIBUTE_EMAIL_VERIFIED_AT => now(),
            User::ATTRIBUTE_PASSWORD => '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', // password
            User::ATTRIBUTE_REMEMBER_TOKEN => Str::random(10),
        ];
    }

    /**
     * Indicate that the model's email address should be unverified.
     *
     * @return static
     */
    public function unverified(): static
    {
        return $this->state(
            fn () => [
                User::ATTRIBUTE_EMAIL_VERIFIED_AT => null,
            ]
        );
    }

    /**
     * Indicate that the user should have a personal team.
     *
     * @return static
     */
    public function withPersonalTeam(): static
    {
        if (! Features::hasTeamFeatures()) {
            return $this->state([]);
        }

        return $this->has(
            Team::factory()
                ->state(
                    fn (array $attributes, User $user) => [
                        'name' => $user->name.'\'s Team',
                        'user_id' => $user->id,
                        'personal_team' => true,
                    ]
                ),
            'ownedTeams'
        );
    }

    /**
     * Create and set current team for user with specified role.
     *
     * @param  string  $role
     * @return static
     */
    public function withCurrentTeam(string $role): static
    {
        return $this->afterCreating(
            function (User $user) use ($role) {
                $team = Team::factory()
                    ->hasAttached($user, ['role' => $role])
                    ->create();

                $user->switchTeam($team);
            }
        );
    }
}
