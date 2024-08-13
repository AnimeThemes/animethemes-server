<?php

declare(strict_types=1);

namespace Database\Factories\Auth;

use App\Models\Auth\Permission;
use App\Models\Auth\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Spatie\Permission\PermissionRegistrar;

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
     * @phpstan-ignore-next-line
     * @return array
     */
    public function definition(): array
    {
        return [
            User::ATTRIBUTE_NAME => fake()->name(),
            User::ATTRIBUTE_EMAIL => fake()->safeEmail(),
            User::ATTRIBUTE_EMAIL_VERIFIED_AT => now(),
            User::ATTRIBUTE_PASSWORD => Hash::make('password'),
            User::ATTRIBUTE_REMEMBER_TOKEN => Str::random(10),
        ];
    }

    /**
     * Create and assign permission to user.
     *
     * @param  string  ...$abilities
     * @return static
     */
    public function withPermissions(string ...$abilities): static
    {
        return $this->afterCreating(
            function (User $user) use ($abilities) {
                $permissions = Arr::map($abilities, fn (string $ability) => Permission::findOrCreate($ability));

                App::make(PermissionRegistrar::class)->forgetCachedPermissions();

                $user->givePermissionTo($permissions);
            }
        );
    }
}
