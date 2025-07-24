<?php

declare(strict_types=1);

namespace Tests\Unit\Models\Wiki\Anime;

use App\Enums\Models\Wiki\AnimeSynonymType;
use App\Models\Wiki\Anime;
use App\Models\Wiki\Anime\AnimeSynonym;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class AnimeSynonymTest extends TestCase
{
    use WithFaker;

    /**
     * The type attribute of a synonym shall be cast to a AnimeSynonymType enum instance.
     */
    public function testCastsTypeToEnum(): void
    {
        $theme = AnimeSynonym::factory()
            ->for(Anime::factory())
            ->createOne();

        $type = $theme->type;

        static::assertInstanceOf(AnimeSynonymType::class, $type);
    }

    /**
     * Synonym shall be a searchable resource.
     */
    public function testSearchableAs(): void
    {
        $synonym = AnimeSynonym::factory()
            ->for(Anime::factory())
            ->createOne();

        static::assertIsString($synonym->searchableAs());
    }

    /**
     * Synonym shall be a searchable resource.
     */
    public function testToSearchableArray(): void
    {
        $synonym = AnimeSynonym::factory()
            ->for(Anime::factory())
            ->createOne();

        static::assertIsArray($synonym->toSearchableArray());
    }

    /**
     * Synonyms shall be nameable.
     */
    public function testNameable(): void
    {
        $synonym = AnimeSynonym::factory()
            ->for(Anime::factory())
            ->createOne();

        static::assertIsString($synonym->getName());
    }

    /**
     * Synonyms shall have subtitle.
     */
    public function testHasSubtitle(): void
    {
        $synonym = AnimeSynonym::factory()
            ->for(Anime::factory())
            ->createOne();

        static::assertIsString($synonym->getSubtitle());
    }

    /**
     * Synonyms shall belong to an Anime.
     */
    public function testAnime(): void
    {
        $synonym = AnimeSynonym::factory()
            ->for(Anime::factory())
            ->createOne();

        static::assertInstanceOf(BelongsTo::class, $synonym->anime());
        static::assertInstanceOf(Anime::class, $synonym->anime()->first());
    }
}
