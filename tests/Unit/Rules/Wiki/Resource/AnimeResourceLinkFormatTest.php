<?php

declare(strict_types=1);

namespace Tests\Unit\Rules\Wiki\Resource;

use App\Enums\Models\Wiki\ResourceSite;
use App\Models\Wiki\Anime;
use App\Rules\Wiki\Resource\AnimeResourceLinkFormatRule;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Tests\TestCase;

/**
 * Class AnimeResourceLinkFormatRule.
 */
class AnimeResourceLinkFormatTest extends TestCase
{
    use WithFaker;

    /**
     * The Anime Resource Link Format Rule shall fail for sites with no defined pattern.
     *
     * @return void
     */
    public function testFailsForNoPattern(): void
    {
        $attribute = $this->faker->word();

        $validator = Validator::make(
            [$attribute => $this->faker->url()],
            [$attribute => new AnimeResourceLinkFormatRule(ResourceSite::YOUTUBE_MUSIC)],
        );

        static::assertFalse($validator->passes());
    }

    /**
     * The Anime Resource Link Format Rule shall pass for URLs that match the expected pattern.
     *
     * @return void
     */
    public function testPassesForPattern(): void
    {
        /** @var ResourceSite $site */
        $site = Arr::random(ResourceSite::getForModel(Anime::class));

        $url = $site->formatResourceLink(Anime::class, $this->faker->randomDigitNotNull(), $this->faker->word(), 'null');

        $attribute = $this->faker->word();

        $validator = Validator::make(
            [$attribute => $url],
            [$attribute => new AnimeResourceLinkFormatRule($site)],
        );

        static::assertTrue($validator->passes());
    }

    /**
     * The Anime Resource Link Format Rule shall fail for trailing slashes in URLs with defined patterns.
     *
     * @return void
     */
    public function testFailsForTrailingSlash(): void
    {
        /** @var ResourceSite $site */
        $site = Arr::random(ResourceSite::getForModel(Anime::class));

        $url = $site->formatResourceLink(Anime::class, $this->faker->randomDigitNotNull(), $this->faker->word());

        $url = Str::of($url)
            ->append('/')
            ->__toString();

        $attribute = $this->faker->word();

        $validator = Validator::make(
            [$attribute => $url],
            [$attribute => new AnimeResourceLinkFormatRule($site)],
        );

        static::assertFalse($site->getPattern(Anime::class) && $validator->passes());
    }

    /**
     * The Anime Resource Link Format Rule shall fail for trailing slugs in URLs with defined patterns.
     *
     * @return void
     */
    public function testFailsForTrailingSlug(): void
    {
        /** @var ResourceSite $site */
        $site = Arr::random(ResourceSite::getForModel(Anime::class));

        $url = $site->formatResourceLink(Anime::class, $this->faker->randomDigitNotNull(), $this->faker->word());

        $url = Str::of($url)
            ->append('/')
            ->append($this->faker->word())
            ->__toString();

        $attribute = $this->faker->word();

        $validator = Validator::make(
            [$attribute => $url],
            [$attribute => new AnimeResourceLinkFormatRule($site)],
        );

        static::assertFalse($site->getPattern(Anime::class) && $validator->passes());
    }

    /**
     * The Anime Resource Link Format Rule shall fail for other resources not allowed.
     *
     * @return void
     */
    public function testFailsForOtherResources(): void
    {
        /** @var ResourceSite $site */
        $site = Arr::random(
            array_filter(
                ResourceSite::cases(),
                fn ($value) => ! in_array($value, ResourceSite::getForModel(Anime::class))
            )
        );

        $url = $site->formatResourceLink(Anime::class, $this->faker->randomDigitNotNull(), $this->faker->word());

        $attribute = $this->faker->word();

        $validator = Validator::make(
            [$attribute => $url],
            [$attribute => new AnimeResourceLinkFormatRule($site)],
        );

        static::assertFalse($validator->passes());
    }
}
