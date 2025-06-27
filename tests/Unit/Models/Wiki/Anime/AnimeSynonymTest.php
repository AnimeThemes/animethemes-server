<?php

declare(strict_types=1);

namespace Tests\Unit\Models\Wiki\Anime;

use App\Enums\Models\Wiki\AnimeSynonymType;
use App\Models\Wiki\Anime;
use App\Models\Wiki\Anime\AnimeSynonym;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

/**
 * Class SynonymTest.
 */
class AnimeSynonymTest extends TestCase
{
    use WithFaker;

    /**
     * The type attribute of a synonym shall be cast to a AnimeSynonymType enum instance.
     *
     * @return void
     */
    public function test_casts_type_to_enum(): void
    {
        $theme = AnimeSynonym::factory()
            ->for(Anime::factory())
            ->createOne();

        $type = $theme->type;

        static::assertInstanceOf(AnimeSynonymType::class, $type);
    }

    /**
     * Synonym shall be a searchable resource.
     *
     * @return void
     */
    public function test_searchable_as(): void
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
    public function test_to_searchable_array(): void
    {
        $synonym = AnimeSynonym::factory()
            ->for(Anime::factory())
            ->createOne();

        static::assertIsArray($synonym->toSearchableArray());
    }

    /**
     * Synonyms shall be nameable.
     *
     * @return void
     */
    public function test_nameable(): void
    {
        $synonym = AnimeSynonym::factory()
            ->for(Anime::factory())
            ->createOne();

        static::assertIsString($synonym->getName());
    }

    /**
     * Synonyms shall have subtitle.
     *
     * @return void
     */
    public function test_has_subtitle(): void
    {
        $synonym = AnimeSynonym::factory()
            ->for(Anime::factory())
            ->createOne();

        static::assertIsString($synonym->getSubtitle());
    }

    /**
     * Synonyms shall belong to an Anime.
     *
     * @return void
     */
    public function test_anime(): void
    {
        $synonym = AnimeSynonym::factory()
            ->for(Anime::factory())
            ->createOne();

        static::assertInstanceOf(BelongsTo::class, $synonym->anime());
        static::assertInstanceOf(Anime::class, $synonym->anime()->first());
    }
}
