<?php

declare(strict_types=1);

namespace Database\Factories\Admin;

use App\Models\Admin\Announcement;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * Class AnnouncementFactory.
 *
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
     * @return array
     */
    public function definition(): array
    {
        return [
            Announcement::ATTRIBUTE_CONTENT => fake()->sentence(),
        ];
    }
}
