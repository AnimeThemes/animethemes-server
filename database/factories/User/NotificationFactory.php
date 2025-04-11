<?php

declare(strict_types=1);

namespace Database\Factories\User;

use App\Models\User\Notification;
use App\Notifications\UserNotification;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * Class NotificationFactory.
 *
 * @method Notification createOne($attributes = [])
 * @method Notification makeOne($attributes = [])
 *
 * @extends Factory<Notification>
 */
class NotificationFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var class-string<Notification>
     */
    protected $model = Notification::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $data = [
            'title' => fake()->text(),
            'body' => fake()->text(),
            'image' => fake()->imageUrl(),
        ];

        return [
            Notification::ATTRIBUTE_ID => Str::uuid()->__toString(),
            Notification::ATTRIBUTE_TYPE => UserNotification::class,
            Notification::ATTRIBUTE_DATA => $data,
        ];
    }
}
