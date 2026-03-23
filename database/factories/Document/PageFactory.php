<?php

declare(strict_types=1);

namespace Database\Factories\Document;

use App\Models\Document\Page;
use Illuminate\Database\Eloquent\Factories\Attributes\UseModel;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @method Page createOne($attributes = [])
 * @method Page makeOne($attributes = [])
 *
 * @extends Factory<Page>
 */
#[UseModel(Page::class)]
class PageFactory extends Factory
{
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
