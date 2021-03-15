<?php

namespace Database\Factories;

use App\Enums\VideoOverlap;
use App\Enums\VideoSource;
use App\Models\Video;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class VideoFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Video::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'basename' => Str::random(),
            'filename' => Str::random(),
            'path' => Str::random(),
            'size' => $this->faker->randomNumber(),
            'resolution' => $this->faker->randomNumber(),
            'nc' => $this->faker->boolean,
            'subbed' => $this->faker->boolean,
            'lyrics' => $this->faker->boolean,
            'uncen' => $this->faker->boolean,
            'source' => VideoSource::getRandomValue(),
            'overlap' => VideoOverlap::getRandomValue(),
        ];
    }
}
