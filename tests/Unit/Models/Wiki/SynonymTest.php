<?php

declare(strict_types=1);

namespace Tests\Unit\Models\Wiki;

use App\Models\Wiki\Anime;
use App\Models\Wiki\Synonym;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Config;
use Tests\TestCase;

/**
 * Class SynonymTest.
 */
class SynonymTest extends TestCase
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
        $synonym = Synonym::factory()
            ->for(Anime::factory())
            ->create();

        static::assertIsString($synonym->searchableAs());
    }

    /**
     * Synonym shall be a searchable resource.
     *
     * @return void
     */
    public function testToSearchableArray()
    {
        $synonym = Synonym::factory()
            ->for(Anime::factory())
            ->create();

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

        $synonym = Synonym::factory()
            ->for(Anime::factory())
            ->create();

        static::assertEquals(1, $synonym->audits->count());
    }

    /**
     * Synonyms shall be nameable.
     *
     * @return void
     */
    public function testNameable()
    {
        $synonym = Synonym::factory()
            ->for(Anime::factory())
            ->create();

        static::assertIsString($synonym->getName());
    }

    /**
     * Synonyms shall belong to an Anime.
     *
     * @return void
     */
    public function testAnime()
    {
        $synonym = Synonym::factory()
            ->for(Anime::factory())
            ->create();

        static::assertInstanceOf(BelongsTo::class, $synonym->anime());
        static::assertInstanceOf(Anime::class, $synonym->anime()->first());
    }
}
