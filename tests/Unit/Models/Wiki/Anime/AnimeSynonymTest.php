<?php

declare(strict_types=1);

namespace Tests\Unit\Models\Wiki\Anime;

use App\Models\Wiki\Anime;
use App\Models\Wiki\Anime\AnimeSynonym;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Config;
use Tests\TestCase;

/**
 * Class SynonymTest.
 */
class AnimeSynonymTest extends TestCase
{
    use RefreshDatabase;
    use WithFaker;

    /**
     * Synonym shall be a searchable resource.
     *
     * @return void
     */
    public function testSearchableAs()
    {
        $synonym = AnimeSynonym::factory()
            ->for(Anime::factory())
            ->createOne();

        static::assertIsString($synonym->searchableAs());
    }

    /**
     * Synonym shall be a searchable resource.
     *
     * @return void
     */
    public function testToSearchableArray()
    {
        $synonym = AnimeSynonym::factory()
            ->for(Anime::factory())
            ->createOne();

        static::assertIsArray($synonym->toSearchableArray());
    }

    /**
     * Synonyms shall be auditable.
     *
     * @return void
     */
    public function testAuditable()
    {
        Config::set('audit.console', true);

        $synonym = AnimeSynonym::factory()
            ->for(Anime::factory())
            ->createOne();

        static::assertEquals(1, $synonym->audits()->count());
    }

    /**
     * Synonyms shall be nameable.
     *
     * @return void
     */
    public function testNameable()
    {
        $synonym = AnimeSynonym::factory()
            ->for(Anime::factory())
            ->createOne();

        static::assertIsString($synonym->getName());
    }

    /**
     * Synonyms shall belong to an Anime.
     *
     * @return void
     */
    public function testAnime()
    {
        $synonym = AnimeSynonym::factory()
            ->for(Anime::factory())
            ->createOne();

        static::assertInstanceOf(BelongsTo::class, $synonym->anime());
        static::assertInstanceOf(Anime::class, $synonym->anime()->first());
    }
}
