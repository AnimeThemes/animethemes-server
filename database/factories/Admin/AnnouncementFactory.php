<?php

declare(strict_types=1);

namespace Database\Factories\Admin;

use App\Models\Admin\Announcement;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * Class AnnouncementFactory.
 */
class AnnouncementFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
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
            'content' => $this->faker->sentence(),
        ];
    }
}
