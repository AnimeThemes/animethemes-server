<?php

declare(strict_types=1);

namespace Database\Factories\Pivots;

use App\Pivots\StudioResource;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * Class StudioResourceFactory.
 * 
 * @method StudioResource createOne($attributes = [])
 * @method StudioResource makeOne($attributes = [])
 */
class StudioResourceFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     * 
     * @var string
     */
    protected $model = StudioResource::class;

    /**
     * Define the model's default state.
     * 
     * @return array
     */
    public function definition(): array
    {
        return [
            StudioResource::ATTRIBUTE_AS => Str::random(),
        ];
    }
}