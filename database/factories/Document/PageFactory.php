<?php

declare(strict_types=1);

namespace Database\Factories\Document;

use App\Models\Document\Page;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @method Page createOne($attributes = [])
 * @method Page makeOne($attributes = [])
 *
 * @extends Factory<Page>
 */
class PageFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var class-string<Page>
     */
    protected $model = Page::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            Page::ATTRIBUTE_BODY => fake()->sentences(3, true),
            Page::ATTRIBUTE_NAME => fake()->words(3, true),
            Page::ATTRIBUTE_SLUG => Str::slug(fake()->text(191), '_'),
        ];
    }
}
