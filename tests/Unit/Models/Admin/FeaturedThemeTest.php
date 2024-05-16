<?php

declare(strict_types=1);

namespace Tests\Unit\Models\Admin;

use App\Models\Admin\FeaturedTheme;
use App\Models\Auth\User;
use App\Models\Wiki\Anime;
use App\Models\Wiki\Anime\AnimeTheme;
use App\Models\Wiki\Anime\Theme\AnimeThemeEntry;
use App\Models\Wiki\Video;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;
use Tests\TestCase;

/**
 * Class FeaturedThemeTest.
 */
class FeaturedThemeTest extends TestCase
{
    /**
     * Featured Themes shall be nameable.
     *
     * @return void
     */
    public function testNameable(): void
    {
        $featuredTheme = FeaturedTheme::factory()->createOne();

        static::assertIsString($featuredTheme->getName());
    }

    /**
     * Featured Themes shall be subnameable.
     *
     * @return void
     */
    public function testSubNameable(): void
    {
        $featuredTheme = FeaturedTheme::factory()->createOne();

        static::assertIsString($featuredTheme->getSubName());
    }

    /**
     * Featured Themes shall cast the end_at attribute to datetime.
     *
     * @return void
     */
    public function testCastsEndAt(): void
    {
        $featuredTheme = FeaturedTheme::factory()->createOne();

        static::assertInstanceOf(Carbon::class, $featuredTheme->end_at);
    }

    /**
     * Featured Themes shall cast the start_at attribute to datetime.
     *
     * @return void
     */
    public function testCastsStartAt(): void
    {
        $featuredTheme = FeaturedTheme::factory()->createOne();

        static::assertInstanceOf(Carbon::class, $featuredTheme->start_at);
    }

    /**
     * Featured themes shall belong to a User.
     *
     * @return void
     */
    public function testUser(): void
    {
        $featuredTheme = FeaturedTheme::factory()
            ->for(User::factory())
            ->createOne();

        static::assertInstanceOf(BelongsTo::class, $featuredTheme->user());
        static::assertInstanceOf(User::class, $featuredTheme->user()->first());
    }

    /**
     * Featured themes shall belong to a Video.
     *
     * @return void
     */
    public function testVideo(): void
    {
        $featuredTheme = FeaturedTheme::factory()
            ->for(Video::factory())
            ->createOne();

        static::assertInstanceOf(BelongsTo::class, $featuredTheme->video());
        static::assertInstanceOf(Video::class, $featuredTheme->video()->first());
    }

    /**
     * Featured themes shall belong to an Entry.
     *
     * @return void
     */
    public function testEntry(): void
    {
        $featuredTheme = FeaturedTheme::factory()
            ->for(AnimeThemeEntry::factory()->for(AnimeTheme::factory()->for(Anime::factory())))
            ->createOne();

        static::assertInstanceOf(BelongsTo::class, $featuredTheme->animethemeentry());
        static::assertInstanceOf(AnimeThemeEntry::class, $featuredTheme->animethemeentry()->first());
    }
}
