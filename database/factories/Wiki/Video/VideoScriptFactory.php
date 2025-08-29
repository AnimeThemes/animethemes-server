<?php

declare(strict_types=1);

namespace Database\Factories\Wiki\Video;

use App\Models\Wiki\Video\VideoScript;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @method VideoScript createOne($attributes = [])
 * @method VideoScript makeOne($attributes = [])
 *
 * @extends Factory<VideoScript>
 */
class VideoScriptFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var class-string<VideoScript>
     */
    protected $model = VideoScript::class;

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
