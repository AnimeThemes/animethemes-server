<?php

declare(strict_types=1);

namespace Database\Factories\List;

use App\Enums\Models\List\ExternalEntryWatchStatus;
use App\Enums\Models\List\ExternalProfileSite;
use App\Enums\Models\List\ExternalProfileVisibility;
use App\Models\List\External\ExternalEntry;
use App\Models\List\ExternalProfile;
use App\Models\Wiki\Anime;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Arr;

/**
 * Class ExternalProfileFactory.
 *
 * @method ExternalProfile createOne($attributes = [])
 * @method ExternalProfile makeOne($attributes = [])
 *
 * @extends Factory<ExternalProfile>
 */
class ExternalProfileFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var class-string<ExternalProfile>
     */
    protected $model = ExternalProfile::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition(): array
    {
        $site = Arr::random(ExternalProfileSite::cases());
        $visibility = Arr::random(ExternalProfileVisibility::cases());

        return [
            ExternalProfile::ATTRIBUTE_NAME => fake()->words(3, true),
            ExternalProfile::ATTRIBUTE_SITE => $site->value,
            ExternalProfile::ATTRIBUTE_VISIBILITY => $visibility->value,
        ];
    }

    /**
     * Define the model's entry listing.
     *
     * @param  int  $count
     * @return static
     */
    public function entries(int $count): static
    {
        return $this->afterCreating(
            function (ExternalProfile $profile) use ($count) {
                foreach (range(1, $count) as $index) {
                    $entry = ExternalEntry::factory()
                        ->for($profile)
                        ->for(Anime::factory())
                        ->createOne();

                    $entry->is_favorite = fake()->boolean();
                    $entry->score = fake()->numberBetween(1, 10);
                    $entry->watch_status = Arr::random(ExternalEntryWatchStatus::cases())->value;
                    $entry->save();
                }
            }
        );
    }

    /**
     * Define the model's entry listing.
     *
     * @param  array  $animeIds
     * @return static
     */
    public function entriesForIds(array $animeIds): static
    {
        return $this->afterCreating(
            function (ExternalProfile $profile) use ($animeIds) {
                foreach ($animeIds as $animeId) {
                    $entry = ExternalEntry::factory()
                        ->for($profile)
                        ->for(Anime::query()->find($animeId))
                        ->createOne();

                    $entry->is_favorite = fake()->boolean();
                    $entry->score = fake()->numberBetween(1, 10);
                    $entry->watch_status = Arr::random(ExternalEntryWatchStatus::cases())->value;
                    $entry->save();
                }
            }
        );
    }
}
