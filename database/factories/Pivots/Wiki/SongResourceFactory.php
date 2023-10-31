<?php

declare(strict_types=1);

namespace Database\Factories\Pivots\Wiki;

use App\Pivots\Wiki\SongResource;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * Class SongResourceFactory.
 *
 * @method SongResource createOne($attributes = [])
 * @method SongResource makeOne($attributes = [])
 *
 * @extends Factory<SongResource>
 */
class SongResourceFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var class-string<SongResource>
     */
    protected $model = SongResource::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition(): array
    {
        return [
            SongResource::ATTRIBUTE_AS => Str::random(),
        ];
    }
}
