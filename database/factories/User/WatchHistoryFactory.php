<?php

declare(strict_types=1);

namespace Database\Factories\User;

use App\Models\Auth\User;
use App\Models\User\WatchHistory;
use App\Models\Wiki\Anime\Theme\AnimeThemeEntry;
use App\Models\Wiki\Video;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @method WatchHistory createOne($attributes = [])
 * @method WatchHistory makeOne($attributes = [])
 *
 * @extends Factory<WatchHistory>
 */
class WatchHistoryFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var class-string<WatchHistory>
     */
    protected $model = WatchHistory::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            WatchHistory::ATTRIBUTE_ENTRY => AnimeThemeEntry::factory(),
            WatchHistory::ATTRIBUTE_USER => User::factory(),
            WatchHistory::ATTRIBUTE_VIDEO => Video::factory(),
        ];
    }
}
