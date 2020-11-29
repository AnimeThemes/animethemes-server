<?php

namespace Database\Factories;

use App\Enums\ImageFacet;
use App\Models\Image;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

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
    public function definition()
    {
        return [
            'path' => Str::random(40).'.png',
            'facet' => ImageFacet::getRandomValue(),
        ];
    }
}
