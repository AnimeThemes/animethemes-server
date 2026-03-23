<?php

declare(strict_types=1);

namespace Database\Factories\Wiki\Video;

use App\Models\Wiki\Video\VideoScript;
use Illuminate\Database\Eloquent\Factories\Attributes\UseModel;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @method VideoScript createOne($attributes = [])
 * @method VideoScript makeOne($attributes = [])
 *
 * @extends Factory<VideoScript>
 */
#[UseModel(VideoScript::class)]
class VideoScriptFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            VideoScript::ATTRIBUTE_PATH => Str::random(),
        ];
    }
}
