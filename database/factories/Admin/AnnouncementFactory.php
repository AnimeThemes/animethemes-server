<?php

declare(strict_types=1);

namespace Database\Factories\Admin;

use App\Models\Admin\Announcement;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @method Announcement createOne($attributes = [])
 * @method Announcement makeOne($attributes = [])
 *
 * @extends Factory<Announcement>
 */
class AnnouncementFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var class-string<Announcement>
     */
    protected $model = Announcement::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            Announcement::ATTRIBUTE_CONTENT => fake()->sentence(),
            Announcement::ATTRIBUTE_PUBLIC => true,
        ];
    }

    /**
     * Set the public state to false.
     */
    public function private(): static
    {
        return $this->state([
            Announcement::ATTRIBUTE_PUBLIC => false,
        ]);
    }
}
