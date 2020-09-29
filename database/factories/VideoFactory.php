<?php

namespace Database\Factories;

use App\Models\Video;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Http\Testing\File;
use Illuminate\Support\Facades\Storage;

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
        $fs = Storage::fake('local');
        $file_name = $this->faker->unique()->word();
        $file = File::fake()->create($file_name . '.webm');
        $fs_file = $fs->put('', $file);
        $fs_pathinfo = pathinfo(strval($fs_file));

        return [
            'basename' => $fs_pathinfo['basename'],
            'filename' => $fs_pathinfo['filename'],
            'path' => $fs_file
        ];
    }
}
