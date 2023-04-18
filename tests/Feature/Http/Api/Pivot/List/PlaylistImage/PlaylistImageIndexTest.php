<?php

declare(strict_types=1);

namespace Http\Api\Pivot\List\PlaylistImage;

use App\Concerns\Actions\Http\Api\SortsModels;
use App\Contracts\Http\Api\Field\SortableField;
use App\Enums\Http\Api\Sort\Direction;
use App\Enums\Models\List\PlaylistVisibility;
use App\Enums\Models\Wiki\ImageFacet;
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
use App\Http\Api\Schema\Pivot\List\PlaylistImageSchema;
use App\Http\Api\Sort\Sort;
use App\Http\Resources\Pivot\List\Collection\PlaylistImageCollection;
use App\Http\Resources\Pivot\List\Resource\PlaylistImageResource;
use App\Models\Auth\User;
use App\Models\List\Playlist;
use App\Models\Wiki\Image;
use App\Pivots\BasePivot;
use App\Pivots\List\PlaylistImage;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Tests\TestCase;

/**
 * Class PlaylistImageIndexTest.
 */
class PlaylistImageIndexTest extends TestCase
{
    use SortsModels;
    use WithFaker;

    /**
     * By default, the Playlist Image Index Endpoint shall return a collection of Playlist Image Resources.
     *
     * @return void
     */
    public function testDefault(): void
    {
        $publicCount = $this->faker->randomDigitNotNull();

        Collection::times($publicCount, function () {
            PlaylistImage::factory()
                ->for(
                    Playlist::factory()
                        ->for(User::factory())
                        ->state([
                            Playlist::ATTRIBUTE_VISIBILITY => PlaylistVisibility::PUBLIC,
                        ])
                )
                ->for(Image::factory())
                ->create();
        });

        Collection::times($this->faker->randomDigitNotNull(), function () {
            PlaylistImage::factory()
                ->for(
                    Playlist::factory()
                        ->for(User::factory())
                        ->state([
                            Playlist::ATTRIBUTE_VISIBILITY => PlaylistVisibility::UNLISTED,
                        ])
                )
                ->for(Image::factory())
                ->create();
        });

        Collection::times($this->faker->randomDigitNotNull(), function () {
            PlaylistImage::factory()
                ->for(
                    Playlist::factory()
                        ->for(User::factory())
                        ->state([
                            Playlist::ATTRIBUTE_VISIBILITY => PlaylistVisibility::PRIVATE,
                        ])
                )
                ->for(Image::factory())
                ->create();
        });

        $playlistImages = PlaylistImage::query()
            ->whereHas(PlaylistImage::RELATION_PLAYLIST, function (Builder $relationBuilder) {
                $relationBuilder->where(Playlist::ATTRIBUTE_VISIBILITY, PlaylistVisibility::PUBLIC);
            })
            ->get();

        $response = $this->get(route('api.playlistimage.index'));

        $response->assertJsonCount($publicCount, PlaylistImageCollection::$wrap);

        $response->assertJson(
            json_decode(
                json_encode(
                    (new PlaylistImageCollection($playlistImages, new Query()))
                        ->response()
                        ->getData()
                ),
                true
            )
        );
    }

    /**
     * The Playlist Image Index Endpoint shall be paginated.
     *
     * @return void
     */
    public function testPaginated(): void
    {
        Collection::times($this->faker->randomDigitNotNull(), function () {
            PlaylistImage::factory()
                ->for(
                    Playlist::factory()
                        ->for(User::factory())
                        ->state([
                            Playlist::ATTRIBUTE_VISIBILITY => PlaylistVisibility::PUBLIC,
                        ])
                )
                ->for(Image::factory())
                ->create();
        });

        $response = $this->get(route('api.playlistimage.index'));

        $response->assertJsonStructure([
            PlaylistImageCollection::$wrap,
            'links',
            'meta',
        ]);
    }

    /**
     * The Playlist Image Index Endpoint shall allow inclusion of related resources.
     *
     * @return void
     */
    public function testAllowedIncludePaths(): void
    {
        $schema = new PlaylistImageSchema();

        $allowedIncludes = collect($schema->allowedIncludes());

        $selectedIncludes = $allowedIncludes->random($this->faker->numberBetween(1, $allowedIncludes->count()));

        $includedPaths = $selectedIncludes->map(fn (AllowedInclude $include) => $include->path());

        $parameters = [
            IncludeParser::param() => $includedPaths->join(','),
        ];

        Collection::times($this->faker->randomDigitNotNull(), function () {
            PlaylistImage::factory()
                ->for(
                    Playlist::factory()
                        ->for(User::factory())
                        ->state([
                            Playlist::ATTRIBUTE_VISIBILITY => PlaylistVisibility::PUBLIC,
                        ])
                )
                ->for(Image::factory())
                ->create();
        });

        $response = $this->get(route('api.playlistimage.index', $parameters));

        $playlistImages = PlaylistImage::with($includedPaths->all())->get();

        $response->assertJson(
            json_decode(
                json_encode(
                    (new PlaylistImageCollection($playlistImages, new Query($parameters)))
                        ->response()
                        ->getData()
                ),
                true
            )
        );
    }

    /**
     * The Playlist Image Index Endpoint shall implement sparse fieldsets.
     *
     * @return void
     */
    public function testSparseFieldsets(): void
    {
        $schema = new PlaylistImageSchema();

        $fields = collect($schema->fields());

        $includedFields = $fields->random($this->faker->numberBetween(1, $fields->count()));

        $parameters = [
            FieldParser::param() => [
                PlaylistImageResource::$wrap => $includedFields->map(fn (Field $field) => $field->getKey())->join(','),
            ],
        ];

        Collection::times($this->faker->randomDigitNotNull(), function () {
            PlaylistImage::factory()
                ->for(
                    Playlist::factory()
                        ->for(User::factory())
                        ->state([
                            Playlist::ATTRIBUTE_VISIBILITY => PlaylistVisibility::PUBLIC,
                        ])
                )
                ->for(Image::factory())
                ->create();
        });

        $response = $this->get(route('api.playlistimage.index', $parameters));

        $playlistImages = PlaylistImage::all();

        $response->assertJson(
            json_decode(
                json_encode(
                    (new PlaylistImageCollection($playlistImages, new Query($parameters)))
                        ->response()
                        ->getData()
                ),
                true
            )
        );
    }

    /**
     * The Playlist Image Index Endpoint shall support sorting resources.
     *
     * @return void
     */
    public function testSorts(): void
    {
        $schema = new PlaylistImageSchema();

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
            PlaylistImage::factory()
                ->for(
                    Playlist::factory()
                        ->for(User::factory())
                        ->state([
                            Playlist::ATTRIBUTE_VISIBILITY => PlaylistVisibility::PUBLIC,
                        ])
                )
                ->for(Image::factory())
                ->create();
        });

        $response = $this->get(route('api.playlistimage.index', $parameters));

        $playlistImages = $this->sort(PlaylistImage::query(), $query, $schema)->get();

        $response->assertJson(
            json_decode(
                json_encode(
                    (new PlaylistImageCollection($playlistImages, $query))
                        ->response()
                        ->getData()
                ),
                true
            )
        );
    }

    /**
     * The Playlist Image Index Endpoint shall support filtering by created_at.
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
                PlaylistImage::factory()
                    ->for(
                        Playlist::factory()
                            ->for(User::factory())
                            ->state([
                                Playlist::ATTRIBUTE_VISIBILITY => PlaylistVisibility::PUBLIC,
                            ])
                    )
                    ->for(Image::factory())
                    ->create();
            });
        });

        Carbon::withTestNow($excludedDate, function () {
            Collection::times($this->faker->randomDigitNotNull(), function () {
                PlaylistImage::factory()
                    ->for(
                        Playlist::factory()
                            ->for(User::factory())
                            ->state([
                                Playlist::ATTRIBUTE_VISIBILITY => PlaylistVisibility::PUBLIC,
                            ])
                    )
                    ->for(Image::factory())
                    ->create();
            });
        });

        $playlistImages = PlaylistImage::query()->where(BasePivot::ATTRIBUTE_CREATED_AT, $createdFilter)->get();

        $response = $this->get(route('api.playlistimage.index', $parameters));

        $response->assertJson(
            json_decode(
                json_encode(
                    (new PlaylistImageCollection($playlistImages, new Query($parameters)))
                        ->response()
                        ->getData()
                ),
                true
            )
        );
    }

    /**
     * The Playlist Image Index Endpoint shall support filtering by updated_at.
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
                PlaylistImage::factory()
                    ->for(
                        Playlist::factory()
                            ->for(User::factory())
                            ->state([
                                Playlist::ATTRIBUTE_VISIBILITY => PlaylistVisibility::PUBLIC,
                            ])
                    )
                    ->for(Image::factory())
                    ->create();
            });
        });

        Carbon::withTestNow($excludedDate, function () {
            Collection::times($this->faker->randomDigitNotNull(), function () {
                PlaylistImage::factory()
                    ->for(
                        Playlist::factory()
                            ->for(User::factory())
                            ->state([
                                Playlist::ATTRIBUTE_VISIBILITY => PlaylistVisibility::PUBLIC,
                            ])
                    )
                    ->for(Image::factory())
                    ->create();
            });
        });

        $playlistImages = PlaylistImage::query()->where(BasePivot::ATTRIBUTE_UPDATED_AT, $updatedFilter)->get();

        $response = $this->get(route('api.playlistimage.index', $parameters));

        $response->assertJson(
            json_decode(
                json_encode(
                    (new PlaylistImageCollection($playlistImages, new Query($parameters)))
                        ->response()
                        ->getData()
                ),
                true
            )
        );
    }

    /**
     * The Playlist Image Index Endpoint shall support constrained eager loading of images by facet.
     *
     * @return void
     */
    public function testImagesByFacet(): void
    {
        $facetFilter = ImageFacet::getRandomInstance();

        $parameters = [
            FilterParser::param() => [
                Image::ATTRIBUTE_FACET => $facetFilter->description,
            ],
            IncludeParser::param() => PlaylistImage::RELATION_IMAGE,
        ];

        Collection::times($this->faker->randomDigitNotNull(), function () {
            PlaylistImage::factory()
                ->for(
                    Playlist::factory()
                        ->for(User::factory())
                        ->state([
                            Playlist::ATTRIBUTE_VISIBILITY => PlaylistVisibility::PUBLIC,
                        ])
                )
                ->for(Image::factory())
                ->create();
        });

        $response = $this->get(route('api.playlistimage.index', $parameters));

        $playlistImages = PlaylistImage::with([
            PlaylistImage::RELATION_IMAGE => function (BelongsTo $query) use ($facetFilter) {
                $query->where(Image::ATTRIBUTE_FACET, $facetFilter->value);
            },
        ])
            ->get();

        $response->assertJson(
            json_decode(
                json_encode(
                    (new PlaylistImageCollection($playlistImages, new Query($parameters)))
                        ->response()
                        ->getData()
                ),
                true
            )
        );
    }
}
