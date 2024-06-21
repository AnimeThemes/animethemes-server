<?php

declare(strict_types=1);

namespace Tests\Unit\Rules\Wiki\Resource;

use App\Enums\Models\Wiki\ResourceSite;
use App\Models\Wiki\Song;
use App\Rules\Wiki\Resource\SongResourceLinkFormatRule;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Tests\TestCase;

/**
 * Class SongResourceLinkFormatTest.
 */
class SongResourceLinkFormatTest extends TestCase
{
    use WithFaker;

    /**
     * The Song Resource Link Format Rule shall fail for sites with no defined pattern.
     *
     * @return void
     */
    public function testFailsForNoPattern(): void
    {
        $attribute = $this->faker->word();

        $validator = Validator::make(
            [$attribute => $this->faker->url()],
            [$attribute => new SongResourceLinkFormatRule(ResourceSite::HULU)],
        );

        static::assertFalse($validator->passes());
    }

    /**
     * The Song Resource Link Format Rule shall pass for URLs that match the expected pattern.
     *
     * @return void
     */
    public function testPassesForPattern(): void
    {
        /** @var ResourceSite $site */
        $site = Arr::random(ResourceSite::getForModel(Song::class));

        $url = $site->formatResourceLink(Song::class, $this->faker->randomDigitNotNull(), $this->faker->word(), 'null');

        $attribute = $this->faker->word();

        $validator = Validator::make(
            [$attribute => $url],
            [$attribute => new SongResourceLinkFormatRule($site)],
        );

        static::assertTrue($validator->passes());
    }

    /**
     * The Song Resource Link Format Rule shall fail for trailing slashes in URLs with defined patterns.
     *
     * @return void
     */
    public function testFailsForTrailingSlash(): void
    {
        /** @var ResourceSite $site */
        $site = Arr::random(ResourceSite::getForModel(Song::class));

        $url = $site->formatResourceLink(Song::class, $this->faker->randomDigitNotNull(), $this->faker->word());

        $url = Str::of($url)
            ->append('/')
            ->__toString();

        $attribute = $this->faker->word();

        $validator = Validator::make(
            [$attribute => $url],
            [$attribute => new SongResourceLinkFormatRule($site)],
        );

        static::assertFalse($site->getPattern(Song::class) && $validator->passes());
    }

    /**
     * The Song Resource Link Format Rule shall fail for trailing slugs in URLs with defined patterns.
     *
     * @return void
     */
    public function testFailsForTrailingSlug(): void
    {
        /** @var ResourceSite $site */
        $site = Arr::random(ResourceSite::getForModel(Song::class));

        $url = $site->formatResourceLink(Song::class, $this->faker->randomDigitNotNull(), $this->faker->word());

        $url = Str::of($url)
            ->append('/')
            ->append($this->faker->word())
            ->__toString();

        $attribute = $this->faker->word();

        $validator = Validator::make(
            [$attribute => $url],
            [$attribute => new SongResourceLinkFormatRule($site)],
        );

        static::assertFalse($site->getPattern(Song::class) && $validator->passes());
    }

    /**
     * The Song Resource Link Format Rule shall fail for other resources.
     *
     * @return void
     */
    public function testFailsForOtherResources(): void
    {
        /** @var ResourceSite $site */
        $site = Arr::random(
            array_filter(
                ResourceSite::cases(),
                fn ($value) => !in_array($value, ResourceSite::getForModel(Song::class))
            )
        );

        $url = $site->formatResourceLink(Song::class, $this->faker->randomDigitNotNull(), $this->faker->word());

        $attribute = $this->faker->word();

        $validator = Validator::make(
            [$attribute => $url],
            [$attribute => new SongResourceLinkFormatRule($site)],
        );

        static::assertFalse($validator->passes());
    }

}
