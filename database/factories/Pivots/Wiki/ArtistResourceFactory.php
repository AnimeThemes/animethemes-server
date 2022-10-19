<?php

declare(strict_types=1);

namespace Database\Factories\Pivots\Wiki;

use App\Pivots\Wiki\ArtistResource;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * Class ArtistResourceFactory.
 *
 * @method ArtistResource createOne($attributes = [])
 * @method ArtistResource makeOne($attributes = [])
 *
 * @extends Factory<ArtistResource>
 */
class ArtistResourceFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var class-string<ArtistResource>
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
            ArtistResource::ATTRIBUTE_AS => Str::random(),
        ];
    }
}
