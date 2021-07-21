<?php

declare(strict_types=1);

namespace Database\Factories\Wiki;

use App\Models\Wiki\Synonym;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * Class SynonymFactory.
 *
 * @method Synonym createOne($attributes = [])
 * @method Synonym makeOne($attributes = [])
 */
class SynonymFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Synonym::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition(): array
    {
        return [
            'text' => $this->faker->words(3, true),
        ];
    }
}
