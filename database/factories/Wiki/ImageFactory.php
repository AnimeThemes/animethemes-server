<?php

declare(strict_types=1);

namespace Database\Factories\Wiki;

use App\Enums\Models\Wiki\ImageFacet;
use App\Models\Wiki\Image;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * Class ImageFactory.
 */
class ImageFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Image::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition(): array
    {
        return [
            'path' => Str::random(),
            'size' => $this->faker->randomNumber(),
            'mimetype' => $this->faker->mimeType,
            'facet' => ImageFacet::getRandomValue(),
        ];
    }
}
