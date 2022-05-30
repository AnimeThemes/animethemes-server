<?php

declare(strict_types=1);

namespace Database\Factories\Auth;

use App\Models\Auth\Permission;
use App\Models\Auth\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\App;
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
     * Create and assign permission to user.
     *
     * @param  string  $ability
     * @return static
     */
    public function withPermission(string $ability): static
    {
        return $this->afterCreating(
            function (User $user) use ($ability) {
                $permission = Permission::findOrCreate($ability);

                App::make(PermissionRegistrar::class)->forgetCachedPermissions();

                $user->givePermissionTo($permission);
            }
        );
    }
}
