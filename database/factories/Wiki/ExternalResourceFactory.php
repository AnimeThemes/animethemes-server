<?php

declare(strict_types=1);

namespace Database\Factories\Wiki;

use App\Enums\Models\Wiki\ResourceSite;
use App\Models\Wiki\ExternalResource;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Arr;

/**
 * Class ExternalResourceFactory.
 *
 * @method ExternalResource createOne($attributes = [])
 * @method ExternalResource makeOne($attributes = [])
 *
 * @extends Factory<ExternalResource>
 */
class ExternalResourceFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var class-string<ExternalResource>
     */
    protected $model = ExternalResource::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $site = Arr::random(ResourceSite::cases());

        $link = fake()->url();

        return [
            ExternalResource::ATTRIBUTE_EXTERNAL_ID => ResourceSite::parseIdFromLink($link),
            ExternalResource::ATTRIBUTE_LINK => $link,
            ExternalResource::ATTRIBUTE_SITE => $site->value,
        ];
    }
}
