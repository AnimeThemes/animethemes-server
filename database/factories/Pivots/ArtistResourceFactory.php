<?php

declare(strict_types=1);

namespace Database\Factories\Pivots;

use App\Pivots\ArtistResource;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * Class ArtistResourceFactory
 * @package Database\Factories\Pivots
 */
class ArtistResourceFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = ArtistResource::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition(): array
    {
        return [
            'as' => Str::random(),
        ];
    }
}
