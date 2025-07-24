<?php

declare(strict_types=1);

namespace Tests\Unit\Rules\Wiki\Resource;

use App\Enums\Models\Wiki\ResourceSite;
use App\Models\Wiki\Artist;
use App\Rules\Wiki\Resource\ArtistResourceLinkFormatRule;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Tests\TestCase;

class ArtistResourceLinkFormatTest extends TestCase
{
    use WithFaker;

    /**
     * The Artist Resource Link Format Rule shall fail for sites with no defined pattern.
     */
    public function testFailsForNoPattern(): void
    {
        $attribute = $this->faker->word();

        $validator = Validator::make(
            [$attribute => $this->faker->url()],
            [$attribute => new ArtistResourceLinkFormatRule(ResourceSite::DISNEY_PLUS)],
        );

        static::assertFalse($validator->passes());
    }

    /**
     * The Artist Resource Link Format Rule shall pass for URLs that match the expected pattern.
     */
    public function testPassesForPattern(): void
    {
        /** @var ResourceSite $site */
        $site = Arr::random(ResourceSite::getForModel(Artist::class));

        $url = $site->formatResourceLink(Artist::class, $this->faker->randomDigitNotNull(), $this->faker->word(), 'null');

        $attribute = $this->faker->word();

        $validator = Validator::make(
            [$attribute => $url],
            [$attribute => new ArtistResourceLinkFormatRule($site)],
        );

        static::assertTrue($validator->passes());
    }

    /**
     * The Artist Resource Link Format Rule shall fail for trailing slashes in URLs with defined patterns.
     */
    public function testFailsForTrailingSlash(): void
    {
        /** @var ResourceSite $site */
        $site = Arr::random(ResourceSite::getForModel(Artist::class));

        $url = $site->formatResourceLink(Artist::class, $this->faker->randomDigitNotNull(), $this->faker->word());

        $url = Str::of($url)
            ->append('/')
            ->__toString();

        $attribute = $this->faker->word();

        $validator = Validator::make(
            [$attribute => $url],
            [$attribute => new ArtistResourceLinkFormatRule($site)],
        );

        static::assertFalse($site->getPattern(Artist::class) && $validator->passes());
    }

    /**
     * The Artist Resource Link Format Rule shall fail for trailing slugs in URLs with defined patterns.
     */
    public function testFailsForTrailingSlug(): void
    {
        /** @var ResourceSite $site */
        $site = Arr::random(ResourceSite::getForModel(Artist::class));

        $url = $site->formatResourceLink(Artist::class, $this->faker->randomDigitNotNull(), $this->faker->word());

        $url = Str::of($url)
            ->append('/')
            ->append($this->faker->word())
            ->__toString();

        $attribute = $this->faker->word();

        $validator = Validator::make(
            [$attribute => $url],
            [$attribute => new ArtistResourceLinkFormatRule($site)],
        );

        static::assertFalse($site->getPattern(Artist::class) && $validator->passes());
    }

    /**
     * The Artist Resource Link Format Rule shall fail for other resources.
     */
    public function testFailsForOtherResources(): void
    {
        /** @var ResourceSite $site */
        $site = Arr::random(
            array_filter(
                ResourceSite::cases(),
                fn ($value) => ! in_array($value, ResourceSite::getForModel(Artist::class))
            )
        );

        $url = $site->formatResourceLink(Artist::class, $this->faker->randomDigitNotNull(), $this->faker->word());

        $attribute = $this->faker->word();

        $validator = Validator::make(
            [$attribute => $url],
            [$attribute => new ArtistResourceLinkFormatRule($site)],
        );

        static::assertFalse($validator->passes());
    }
}
