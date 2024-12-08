<?php

declare(strict_types=1);

namespace Database\Factories\Pivots\Wiki;

use App\Pivots\Wiki\StudioImage;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * Class StudioImageFactory.
 *
 * @method StudioImage createOne($attributes = [])
 * @method StudioImage makeOne($attributes = [])
 *
 * @extends Factory<StudioImage>
 */
class StudioImageFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var class-string<StudioImage>
     */
    protected $model = StudioImage::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            //
        ];
    }
}
