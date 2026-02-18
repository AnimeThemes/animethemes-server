<?php

declare(strict_types=1);

namespace Database\Factories\Wiki;

use App\Enums\Models\Wiki\SynonymType;
use App\Models\Wiki\Synonym;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Arr;

/**
 * @method Synonym createOne($attributes = [])
 * @method Synonym makeOne($attributes = [])
 *
 * @extends Factory<Synonym>
 */
class SynonymFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var class-string<Synonym>
     */
    protected $model = Synonym::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $type = Arr::random(SynonymType::cases());

        return [
            Synonym::ATTRIBUTE_TEXT => fake()->words(3, true),
            Synonym::ATTRIBUTE_TYPE => $type->value,
        ];
    }
}
