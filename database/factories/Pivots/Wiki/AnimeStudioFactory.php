<?php

declare(strict_types=1);

namespace Database\Factories\Pivots\Wiki;

use App\Pivots\Wiki\AnimeStudio;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * Class AnimeStudioFactory.
 *
 * @method AnimeStudio createOne($attributes = [])
 * @method AnimeStudio makeOne($attributes = [])
 *
 * @extends Factory<AnimeStudio>
 */
class AnimeStudioFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var class-string<AnimeStudio>
     */
    protected $model = AnimeStudio::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition(): array
    {
        return [
            //
        ];
    }
}
