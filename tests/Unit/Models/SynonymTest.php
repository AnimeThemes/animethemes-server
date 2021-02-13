<?php

namespace Tests\Unit\Models;

use App\Models\Anime;
use App\Models\Synonym;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Config;
use Tests\TestCase;

class SynonymTest extends TestCase
{
    use RefreshDatabase, WithFaker;

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

        $this->assertIsString($synonym->searchableAs());
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

        $this->assertIsArray($synonym->toSearchableArray());
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

        $this->assertEquals(1, $synonym->audits->count());
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

        $this->assertIsString($synonym->getName());
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

        $this->assertInstanceOf(BelongsTo::class, $synonym->anime());
        $this->assertInstanceOf(Anime::class, $synonym->anime()->first());
    }
}
