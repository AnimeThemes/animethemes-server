<?php

namespace Database\Factories;

use App\Enums\ImageFacet;
use App\Models\Image;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Http\Testing\File;
use Illuminate\Support\Facades\Storage;

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
        $fs = Storage::fake('images');
        $file = File::fake()->image($this->faker->word());
        $fs_file = $fs->put('', $file);

        return [
            'path' => $fs_file,
            'facet' => ImageFacet::getRandomValue(),
        ];
    }
}
