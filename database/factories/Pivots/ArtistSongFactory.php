<?php

namespace Database\Factories\Pivots;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class ArtistSongFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = \App\Pivots\ArtistSong::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'as' => Str::random(),
        ];
    }
}
