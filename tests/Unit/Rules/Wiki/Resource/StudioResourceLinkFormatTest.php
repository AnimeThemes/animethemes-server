<?php

declare(strict_types=1);

namespace Tests\Unit\Rules\Wiki\Resource;

use App\Enums\Models\Wiki\ResourceSite;
use App\Models\Wiki\Studio;
use App\Rules\Wiki\Resource\StudioResourceLinkFormatRule;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Tests\TestCase;

/**
 * Class StudioResourceLinkFormatTest.
 */
class StudioResourceLinkFormatTest extends TestCase
{
    use WithFaker;

    /**
     * The Studio Resource Link Format Rule shall fail for sites with no defined pattern.
     *
     * @return void
     */
    public function testFailsForNoPattern(): void
    {
        $attribute = $this->faker->word();

        $validator = Validator::make(
            [$attribute => $this->faker->url()],
            [$attribute => new StudioResourceLinkFormatRule(ResourceSite::SPOTIFY)],
        );

        static::assertFalse($validator->passes());
    }

    /**
     * The Studio Resource Link Format Rule shall pass for URLs that match the expected pattern.
     *
     * @return void
     */
    public function testPassesForPattern(): void
    {
        /** @var ResourceSite $site */
        $site = Arr::random(ResourceSite::getForModel(Studio::class));

        $url = $site->formatResourceLink(Studio::class, $this->faker->randomDigitNotNull(), $this->faker->word(), 'null');

        $attribute = $this->faker->word();

        $validator = Validator::make(
            [$attribute => $url],
            [$attribute => new StudioResourceLinkFormatRule($site)],
        );

        static::assertTrue($validator->passes());
    }

    /**
     * The Studio Resource Link Format Rule shall fail for trailing slashes in URLs with defined patterns.
     *
     * @return void
     */
    public function testFailsForTrailingSlash(): void
    {
        /** @var ResourceSite $site */
        $site = Arr::random(ResourceSite::getForModel(Studio::class));

        $url = $site->formatResourceLink(Studio::class, $this->faker->randomDigitNotNull(), $this->faker->word());

        $url = Str::of($url)
            ->append('/')
            ->__toString();

        $attribute = $this->faker->word();

        $validator = Validator::make(
            [$attribute => $url],
            [$attribute => new StudioResourceLinkFormatRule($site)],
        );

        static::assertFalse($site->getPattern(Studio::class) && $validator->passes());
    }

    /**
     * The Studio Resource Link Format Rule shall fail for trailing slugs in URLs with defined patterns.
     *
     * @return void
     */
    public function testFailsForTrailingSlug(): void
    {
        /** @var ResourceSite $site */
        $site = Arr::random(ResourceSite::getForModel(Studio::class));

        $url = $site->formatResourceLink(Studio::class, $this->faker->randomDigitNotNull(), $this->faker->word());

        $url = Str::of($url)
            ->append('/')
            ->append($this->faker->word())
            ->__toString();

        $attribute = $this->faker->word();

        $validator = Validator::make(
            [$attribute => $url],
            [$attribute => new StudioResourceLinkFormatRule($site)],
        );

        static::assertFalse($site->getPattern(Studio::class) && $validator->passes());
    }

    /**
     * The Studio Resource Link Format Rule shall fail for other resources.
     *
     * @return void
     */
    public function testFailsForOtherResources(): void
    {
        /** @var ResourceSite $site */
        $site = Arr::random(
            array_filter(
                ResourceSite::cases(),
                fn ($value) => !in_array($value, ResourceSite::getForModel(Studio::class))
            )
        );

        $url = $site->formatResourceLink(Studio::class, $this->faker->randomDigitNotNull(), $this->faker->word());

        $attribute = $this->faker->word();

        $validator = Validator::make(
            [$attribute => $url],
            [$attribute => new StudioResourceLinkFormatRule($site)],
        );

        static::assertFalse($validator->passes());
    }
}
