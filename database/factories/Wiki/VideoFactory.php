<?php

declare(strict_types=1);

namespace Database\Factories\Wiki;

use App\Enums\Models\Wiki\VideoOverlap;
use App\Enums\Models\Wiki\VideoSource;
use App\Models\Wiki\Video;
use Illuminate\Database\Eloquent\Factories\Attributes\UseModel;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;

/**
 * @method Video createOne($attributes = [])
 * @method Video makeOne($attributes = [])
 *
 * @extends Factory<Video>
 */
#[UseModel(Video::class)]
class VideoFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $overlap = Arr::random(VideoOverlap::cases());
        $source = Arr::random(VideoSource::cases());

        return [
            Video::ATTRIBUTE_BASENAME => Str::random(),
            Video::ATTRIBUTE_FILENAME => Str::random(),
            Video::ATTRIBUTE_LYRICS => fake()->boolean(),
            Video::ATTRIBUTE_MIMETYPE => fake()->mimeType(),
            Video::ATTRIBUTE_NC => fake()->boolean(),
            Video::ATTRIBUTE_OVERLAP => $overlap->value,
            Video::ATTRIBUTE_PATH => Str::random(),
            Video::ATTRIBUTE_RESOLUTION => fake()->numberBetween(360, 1080),
            Video::ATTRIBUTE_SIZE => fake()->randomDigitNotZero(),
            Video::ATTRIBUTE_SOURCE => $source->value,
            Video::ATTRIBUTE_SUBBED => fake()->boolean(),
            Video::ATTRIBUTE_UNCEN => fake()->boolean(),
        ];
    }
}
