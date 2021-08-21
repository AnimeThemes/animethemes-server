<?php

declare(strict_types=1);

namespace Database\Factories\Pivots;

use App\Pivots\AnimeStudio;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * Class AnimeStudioFactory.
 *
 * @method AnimeStudio createOne($attributes = [])
 * @method AnimeStudio makeOne($attributes = [])
 */
class AnimeStudioFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
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
