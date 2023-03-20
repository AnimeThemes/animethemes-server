<?php

declare(strict_types=1);

namespace Http\Api\Pivot\Wiki\ArtistSong;

use App\Concerns\Actions\Http\Api\SortsModels;
use App\Contracts\Http\Api\Field\SortableField;
use App\Enums\Http\Api\Sort\Direction;
use App\Http\Api\Criteria\Paging\Criteria;
use App\Http\Api\Criteria\Paging\OffsetCriteria;
use App\Http\Api\Field\Field;
use App\Http\Api\Include\AllowedInclude;
use App\Http\Api\Parser\FieldParser;
use App\Http\Api\Parser\FilterParser;
use App\Http\Api\Parser\IncludeParser;
use App\Http\Api\Parser\PagingParser;
use App\Http\Api\Parser\SortParser;
use App\Http\Api\Query\Query;
use App\Http\Api\Schema\Pivot\Wiki\ArtistSongSchema;
use App\Http\Api\Sort\Sort;
use App\Http\Resources\Pivot\Wiki\Collection\ArtistSongCollection;
use App\Http\Resources\Pivot\Wiki\Resource\ArtistSongResource;
use App\Models\Wiki\Artist;
use App\Models\Wiki\Song;
use App\Pivots\BasePivot;
use App\Pivots\Wiki\ArtistSong;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\WithoutEvents;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Tests\TestCase;

/**
 * Class ArtistSongIndexTest.
 */
class ArtistSongIndexTest extends TestCase
{
    use SortsModels;
    use WithFaker;
    use WithoutEvents;

    /**
     * By default, the Artist Song Index Endpoint shall return a collection of Artist Song Resources.
     *
     * @return void
     */
    public function testDefault(): void
    {
        Collection::times($this->faker->randomDigitNotNull(), function () {
            ArtistSong::factory()
                ->for(Artist::factory())
                ->for(Song::factory())
                ->create();
        });

        $artistSongs = ArtistSong::all();

        $response = $this->get(route('api.artistsong.index'));

        $response->assertJson(
            json_decode(
                json_encode(
                    (new ArtistSongCollection($artistSongs, new Query()))
                        ->response()
                        ->getData()
                ),
                true
            )
        );
    }

    /**
     * The Artist Song Index Endpoint shall be paginated.
     *
     * @return void
     */
    public function testPaginated(): void
    {
        Collection::times($this->faker->randomDigitNotNull(), function () {
            ArtistSong::factory()
                ->for(Artist::factory())
                ->for(Song::factory())
                ->create();
        });

        $response = $this->get(route('api.artistsong.index'));

        $response->assertJsonStructure([
            ArtistSongCollection::$wrap,
            'links',
            'meta',
        ]);
    }

    /**
     * The Artist Song Index Endpoint shall allow inclusion of related resources.
     *
     * @return void
     */
    public function testAllowedIncludePaths(): void
    {
        $schema = new ArtistSongSchema();

        $allowedIncludes = collect($schema->allowedIncludes());

        $selectedIncludes = $allowedIncludes->random($this->faker->numberBetween(1, $allowedIncludes->count()));

        $includedPaths = $selectedIncludes->map(fn (AllowedInclude $include) => $include->path());

        $parameters = [
            IncludeParser::param() => $includedPaths->join(','),
        ];

        Collection::times($this->faker->randomDigitNotNull(), function () {
            ArtistSong::factory()
                ->for(Artist::factory())
                ->for(Song::factory())
                ->create();
        });

        $response = $this->get(route('api.artistsong.index', $parameters));

        $artistSongs = ArtistSong::with($includedPaths->all())->get();

        $response->assertJson(
            json_decode(
                json_encode(
                    (new ArtistSongCollection($artistSongs, new Query($parameters)))
                        ->response()
                        ->getData()
                ),
                true
            )
        );
    }

    /**
     * The Artist Song Index Endpoint shall implement sparse fieldsets.
     *
     * @return void
     */
    public function testSparseFieldsets(): void
    {
        $schema = new ArtistSongSchema();

        $fields = collect($schema->fields());

        $includedFields = $fields->random($this->faker->numberBetween(1, $fields->count()));

        $parameters = [
            FieldParser::param() => [
                ArtistSongResource::$wrap => $includedFields->map(fn (Field $field) => $field->getKey())->join(','),
            ],
        ];

        Collection::times($this->faker->randomDigitNotNull(), function () {
            ArtistSong::factory()
                ->for(Artist::factory())
                ->for(Song::factory())
                ->create();
        });

        $response = $this->get(route('api.artistsong.index', $parameters));

        $artistSongs = ArtistSong::all();

        $response->assertJson(
            json_decode(
                json_encode(
                    (new ArtistSongCollection($artistSongs, new Query($parameters)))
                        ->response()
                        ->getData()
                ),
                true
            )
        );
    }

    /**
     * The Artist Song Index Endpoint shall support sorting resources.
     *
     * @return void
     */
    public function testSorts(): void
    {
        $schema = new ArtistSongSchema();

        /** @var Sort $sort */
        $sort = collect($schema->fields())
            ->filter(fn (Field $field) => $field instanceof SortableField)
            ->map(fn (SortableField $field) => $field->getSort())
            ->random();

        $parameters = [
            SortParser::param() => $sort->format(Direction::getRandomInstance()),
        ];

        $query = new Query($parameters);

        Collection::times($this->faker->randomDigitNotNull(), function () {
            ArtistSong::factory()
                ->for(Artist::factory())
                ->for(Song::factory())
                ->create();
        });

        $response = $this->get(route('api.artistsong.index', $parameters));

        $artistSongs = $this->sort(ArtistSong::query(), $query, $schema)->get();

        $response->assertJson(
            json_decode(
                json_encode(
                    (new ArtistSongCollection($artistSongs, $query))
                        ->response()
                        ->getData()
                ),
                true
            )
        );
    }

    /**
     * The Artist Song Index Endpoint shall support filtering by created_at.
     *
     * @return void
     */
    public function testCreatedAtFilter(): void
    {
        $createdFilter = $this->faker->date();
        $excludedDate = $this->faker->date();

        $parameters = [
            FilterParser::param() => [
                BasePivot::ATTRIBUTE_CREATED_AT => $createdFilter,
            ],
            PagingParser::param() => [
                OffsetCriteria::SIZE_PARAM => Criteria::MAX_RESULTS,
            ],
        ];

        Carbon::withTestNow($createdFilter, function () {
            Collection::times($this->faker->randomDigitNotNull(), function () {
                ArtistSong::factory()
                    ->for(Artist::factory())
                    ->for(Song::factory())
                    ->create();
            });
        });

        Carbon::withTestNow($excludedDate, function () {
            Collection::times($this->faker->randomDigitNotNull(), function () {
                ArtistSong::factory()
                    ->for(Artist::factory())
                    ->for(Song::factory())
                    ->create();
            });
        });

        $artistSongs = ArtistSong::query()->where(BasePivot::ATTRIBUTE_CREATED_AT, $createdFilter)->get();

        $response = $this->get(route('api.artistsong.index', $parameters));

        $response->assertJson(
            json_decode(
                json_encode(
                    (new ArtistSongCollection($artistSongs, new Query($parameters)))
                        ->response()
                        ->getData()
                ),
                true
            )
        );
    }

    /**
     * The Artist Song Index Endpoint shall support filtering by updated_at.
     *
     * @return void
     */
    public function testUpdatedAtFilter(): void
    {
        $updatedFilter = $this->faker->date();
        $excludedDate = $this->faker->date();

        $parameters = [
            FilterParser::param() => [
                BasePivot::ATTRIBUTE_UPDATED_AT => $updatedFilter,
            ],
            PagingParser::param() => [
                OffsetCriteria::SIZE_PARAM => Criteria::MAX_RESULTS,
            ],
        ];

        Carbon::withTestNow($updatedFilter, function () {
            Collection::times($this->faker->randomDigitNotNull(), function () {
                ArtistSong::factory()
                    ->for(Artist::factory())
                    ->for(Song::factory())
                    ->create();
            });
        });

        Carbon::withTestNow($excludedDate, function () {
            Collection::times($this->faker->randomDigitNotNull(), function () {
                ArtistSong::factory()
                    ->for(Artist::factory())
                    ->for(Song::factory())
                    ->create();
            });
        });

        $artistSongs = ArtistSong::query()->where(BasePivot::ATTRIBUTE_UPDATED_AT, $updatedFilter)->get();

        $response = $this->get(route('api.artistsong.index', $parameters));

        $response->assertJson(
            json_decode(
                json_encode(
                    (new ArtistSongCollection($artistSongs, new Query($parameters)))
                        ->response()
                        ->getData()
                ),
                true
            )
        );
    }
}
