<?php

declare(strict_types=1);

namespace Database\Factories\Pivots\Wiki;

use App\Pivots\Wiki\StudioResource;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * Class StudioResourceFactory.
 *
 * @method StudioResource createOne($attributes = [])
 * @method StudioResource makeOne($attributes = [])
 *
 * @extends Factory<StudioResource>
 */
class StudioResourceFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var class-string<StudioResource>
     */
    protected $model = StudioResource::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            StudioResource::ATTRIBUTE_AS => Str::random(),
        ];
    }
}
