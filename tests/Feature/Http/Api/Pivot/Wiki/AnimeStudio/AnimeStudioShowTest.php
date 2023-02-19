<?php

declare(strict_types=1);

namespace Http\Api\Pivot\Wiki\AnimeStudio;

use App\Enums\Models\Wiki\AnimeSeason;
use App\Http\Api\Field\Field;
use App\Http\Api\Include\AllowedInclude;
use App\Http\Api\Parser\FieldParser;
use App\Http\Api\Parser\FilterParser;
use App\Http\Api\Parser\IncludeParser;
use App\Http\Api\Query\Query;
use App\Http\Api\Schema\Pivot\Wiki\AnimeStudioSchema;
use App\Http\Resources\Pivot\Wiki\Resource\AnimeStudioResource;
use App\Models\Wiki\Anime;
use App\Models\Wiki\Studio;
use App\Pivots\Wiki\AnimeStudio;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\WithoutEvents;
use Tests\TestCase;

/**
 * Class AnimeStudioShowTest.
 */
class AnimeStudioShowTest extends TestCase
{
    use WithFaker;
    use WithoutEvents;

    /**
     * The Anime Studio Show Endpoint shall return an error if the anime studio does not exist.
     *
     * @return void
     */
    public function testNotFound(): void
    {
        $anime = Anime::factory()->createOne();
        $studio = Studio::factory()->createOne();

        $response = $this->get(route('api.animestudio.show', ['anime' => $anime, 'studio' => $studio]));

        $response->assertNotFound();
    }

    /**
     * By default, the Anime Studio Show Endpoint shall return an Anime Studio Resource.
     *
     * @return void
     */
    public function testDefault(): void
    {
        $animeStudio = AnimeStudio::factory()
            ->for(Anime::factory())
            ->for(Studio::factory())
            ->createOne();

        $response = $this->get(route('api.animestudio.show', ['anime' => $animeStudio->anime, 'studio' => $animeStudio->studio]));

        $animeStudio->unsetRelations();

        $response->assertJson(
            json_decode(
                json_encode(
                    (new AnimeStudioResource($animeStudio, new Query()))
                        ->response()
                        ->getData()
                ),
                true
            )
        );
    }

    /**
     * The Anime Studio Show Endpoint shall allow inclusion of related resources.
     *
     * @return void
     */
    public function testAllowedIncludePaths(): void
    {
        $schema = new AnimeStudioSchema();

        $allowedIncludes = collect($schema->allowedIncludes());

        $selectedIncludes = $allowedIncludes->random($this->faker->numberBetween(1, $allowedIncludes->count()));

        $includedPaths = $selectedIncludes->map(fn (AllowedInclude $include) => $include->path());

        $parameters = [
            IncludeParser::param() => $includedPaths->join(','),
        ];

        $animeStudio = AnimeStudio::factory()
            ->for(Anime::factory())
            ->for(Studio::factory())
            ->createOne();

        $response = $this->get(route('api.animestudio.show', ['anime' => $animeStudio->anime, 'studio' => $animeStudio->studio] + $parameters));

        $animeStudio->unsetRelations()->load($includedPaths->all());

        $response->assertJson(
            json_decode(
                json_encode(
                    (new AnimeStudioResource($animeStudio, new Query($parameters)))
                        ->response()
                        ->getData()
                ),
                true
            )
        );
    }

    /**
     * The Anime Studio Show Endpoint shall implement sparse fieldsets.
     *
     * @return void
     */
    public function testSparseFieldsets(): void
    {
        $schema = new AnimeStudioSchema();

        $fields = collect($schema->fields());

        $includedFields = $fields->random($this->faker->numberBetween(1, $fields->count()));

        $parameters = [
            FieldParser::param() => [
                AnimeStudioResource::$wrap => $includedFields->map(fn (Field $field) => $field->getKey())->join(','),
            ],
        ];

        $animeStudio = AnimeStudio::factory()
            ->for(Anime::factory())
            ->for(Studio::factory())
            ->createOne();

        $response = $this->get(route('api.animestudio.show', ['anime' => $animeStudio->anime, 'studio' => $animeStudio->studio] + $parameters));

        $animeStudio->unsetRelations();

        $response->assertJson(
            json_decode(
                json_encode(
                    (new AnimeStudioResource($animeStudio, new Query($parameters)))
                        ->response()
                        ->getData()
                ),
                true
            )
        );
    }

    /**
     * The Anime Studio Show Endpoint shall support constrained eager loading of anime by season.
     *
     * @return void
     */
    public function testAnimeBySeason(): void
    {
        $seasonFilter = AnimeSeason::getRandomInstance();

        $parameters = [
            FilterParser::param() => [
                Anime::ATTRIBUTE_SEASON => $seasonFilter->description,
            ],
            IncludeParser::param() => AnimeStudio::RELATION_ANIME,
        ];

        $animeStudio = AnimeStudio::factory()
            ->for(Anime::factory())
            ->for(Studio::factory())
            ->createOne();

        $response = $this->get(route('api.animestudio.show', ['anime' => $animeStudio->anime, 'studio' => $animeStudio->studio] + $parameters));

        $animeStudio->unsetRelations()->load([
            AnimeStudio::RELATION_ANIME => function (BelongsTo $query) use ($seasonFilter) {
                $query->where(Anime::ATTRIBUTE_SEASON, $seasonFilter->value);
            },
        ]);

        $response->assertJson(
            json_decode(
                json_encode(
                    (new AnimeStudioResource($animeStudio, new Query($parameters)))
                        ->response()
                        ->getData()
                ),
                true
            )
        );
    }

    /**
     * The Anime Studio Show Endpoint shall support constrained eager loading of anime by year.
     *
     * @return void
     */
    public function testAnimeByYear(): void
    {
        $yearFilter = intval($this->faker->year());
        $excludedYear = $yearFilter + 1;

        $parameters = [
            FilterParser::param() => [
                Anime::ATTRIBUTE_YEAR => $yearFilter,
            ],
            IncludeParser::param() => AnimeStudio::RELATION_ANIME,
        ];

        $animeStudio = AnimeStudio::factory()
            ->for(
                Anime::factory()
                    ->state([
                        Anime::ATTRIBUTE_YEAR => $this->faker->boolean() ? $yearFilter : $excludedYear,
                    ])
            )
            ->for(Studio::factory())
            ->createOne();

        $response = $this->get(route('api.animestudio.show', ['anime' => $animeStudio->anime, 'studio' => $animeStudio->studio] + $parameters));

        $animeStudio->unsetRelations()->load([
            AnimeStudio::RELATION_ANIME => function (BelongsTo $query) use ($yearFilter) {
                $query->where(Anime::ATTRIBUTE_YEAR, $yearFilter);
            },
        ]);

        $response->assertJson(
            json_decode(
                json_encode(
                    (new AnimeStudioResource($animeStudio, new Query($parameters)))
                        ->response()
                        ->getData()
                ),
                true
            )
        );
    }
}
