<?php

declare(strict_types=1);

namespace Database\Factories\Wiki;

use App\Enums\Models\Wiki\ImageFacet;
use App\Models\Wiki\Image;
use Illuminate\Database\Eloquent\Factories\Attributes\UseModel;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;

/**
 * @method Image createOne($attributes = [])
 * @method Image makeOne($attributes = [])
 *
 * @extends Factory<Image>
 */
#[UseModel(Image::class)]
class ImageFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $facet = Arr::random(ImageFacet::cases());

        return [
            Image::ATTRIBUTE_FACET => $facet->value,
            Image::ATTRIBUTE_PATH => Str::random(),
        ];
    }
}
