<?php

declare(strict_types=1);

namespace Tests\Feature\Http\Api\Pivot\Wiki\ArtistMember;

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
use App\Http\Api\Schema\Pivot\Wiki\ArtistMemberSchema;
use App\Http\Api\Sort\Sort;
use App\Http\Resources\Pivot\Wiki\Collection\ArtistMemberCollection;
use App\Http\Resources\Pivot\Wiki\Resource\ArtistMemberResource;
use App\Models\Wiki\Artist;
use App\Pivots\BasePivot;
use App\Pivots\Wiki\ArtistMember;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Arr;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Tests\TestCase;

/**
 * Class ArtistMemberIndexTest.
 */
class ArtistMemberIndexTest extends TestCase
{
    use SortsModels;
    use WithFaker;

    /**
     * By default, the Artist Member Index Endpoint shall return a collection of Artist Member Resources.
     *
     * @return void
     */
    public function testDefault(): void
    {
        Collection::times($this->faker->randomDigitNotNull(), function () {
            ArtistMember::factory()
                ->for(Artist::factory(), ArtistMember::RELATION_ARTIST)
                ->for(Artist::factory(), ArtistMember::RELATION_MEMBER)
                ->create();
        });

        $artistMembers = ArtistMember::all();

        $response = $this->get(route('api.artistmember.index'));

        $response->assertJson(
            json_decode(
                json_encode(
                    (new ArtistMemberCollection($artistMembers, new Query()))
                        ->response()
                        ->getData()
                ),
                true
            )
        );
    }

    /**
     * The Artist Member Index Endpoint shall be paginated.
     *
     * @return void
     */
    public function testPaginated(): void
    {
        Collection::times($this->faker->randomDigitNotNull(), function () {
            ArtistMember::factory()
                ->for(Artist::factory(), ArtistMember::RELATION_ARTIST)
                ->for(Artist::factory(), ArtistMember::RELATION_MEMBER)
                ->create();
        });

        $response = $this->get(route('api.artistmember.index'));

        $response->assertJsonStructure([
            ArtistMemberCollection::$wrap,
            'links',
            'meta',
        ]);
    }

    /**
     * The Artist Member Index Endpoint shall allow inclusion of related resources.
     *
     * @return void
     */
    public function testAllowedIncludePaths(): void
    {
        $schema = new ArtistMemberSchema();

        $allowedIncludes = collect($schema->allowedIncludes());

        $selectedIncludes = $allowedIncludes->random($this->faker->numberBetween(1, $allowedIncludes->count()));

        $includedPaths = $selectedIncludes->map(fn (AllowedInclude $include) => $include->path());

        $parameters = [
            IncludeParser::param() => $includedPaths->join(','),
        ];

        Collection::times($this->faker->randomDigitNotNull(), function () {
            ArtistMember::factory()
                ->for(Artist::factory(), ArtistMember::RELATION_ARTIST)
                ->for(Artist::factory(), ArtistMember::RELATION_MEMBER)
                ->create();
        });

        $response = $this->get(route('api.artistmember.index', $parameters));

        $artistMembers = ArtistMember::with($includedPaths->all())->get();

        $response->assertJson(
            json_decode(
                json_encode(
                    (new ArtistMemberCollection($artistMembers, new Query($parameters)))
                        ->response()
                        ->getData()
                ),
                true
            )
        );
    }

    /**
     * The Artist Member Index Endpoint shall implement sparse fieldsets.
     *
     * @return void
     */
    public function testSparseFieldsets(): void
    {
        $schema = new ArtistMemberSchema();

        $fields = collect($schema->fields());

        $includedFields = $fields->random($this->faker->numberBetween(1, $fields->count()));

        $parameters = [
            FieldParser::param() => [
                ArtistMemberResource::$wrap => $includedFields->map(fn (Field $field) => $field->getKey())->join(','),
            ],
        ];

        Collection::times($this->faker->randomDigitNotNull(), function () {
            ArtistMember::factory()
                ->for(Artist::factory(), ArtistMember::RELATION_ARTIST)
                ->for(Artist::factory(), ArtistMember::RELATION_MEMBER)
                ->create();
        });

        $response = $this->get(route('api.artistmember.index', $parameters));

        $artistMembers = ArtistMember::all();

        $response->assertJson(
            json_decode(
                json_encode(
                    (new ArtistMemberCollection($artistMembers, new Query($parameters)))
                        ->response()
                        ->getData()
                ),
                true
            )
        );
    }

    /**
     * The Artist Member Index Endpoint shall support sorting resources.
     *
     * @return void
     */
    public function testSorts(): void
    {
        $schema = new ArtistMemberSchema();

        /** @var Sort $sort */
        $sort = collect($schema->fields())
            ->filter(fn (Field $field) => $field instanceof SortableField)
            ->map(fn (SortableField $field) => $field->getSort())
            ->random();

        $parameters = [
            SortParser::param() => $sort->format(Arr::random(Direction::cases())),
        ];

        $query = new Query($parameters);

        Collection::times($this->faker->randomDigitNotNull(), function () {
            ArtistMember::factory()
                ->for(Artist::factory(), ArtistMember::RELATION_ARTIST)
                ->for(Artist::factory(), ArtistMember::RELATION_MEMBER)
                ->create();
        });

        $response = $this->get(route('api.artistmember.index', $parameters));

        $artistMembers = $this->sort(ArtistMember::query(), $query, $schema)->get();

        $response->assertJson(
            json_decode(
                json_encode(
                    (new ArtistMemberCollection($artistMembers, $query))
                        ->response()
                        ->getData()
                ),
                true
            )
        );
    }

    /**
     * The Artist Member Index Endpoint shall support filtering by created_at.
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
                ArtistMember::factory()
                    ->for(Artist::factory(), ArtistMember::RELATION_ARTIST)
                    ->for(Artist::factory(), ArtistMember::RELATION_MEMBER)
                    ->create();
            });
        });

        Carbon::withTestNow($excludedDate, function () {
            Collection::times($this->faker->randomDigitNotNull(), function () {
                ArtistMember::factory()
                    ->for(Artist::factory(), ArtistMember::RELATION_ARTIST)
                    ->for(Artist::factory(), ArtistMember::RELATION_MEMBER)
                    ->create();
            });
        });

        $artistMembers = ArtistMember::query()->where(BasePivot::ATTRIBUTE_CREATED_AT, $createdFilter)->get();

        $response = $this->get(route('api.artistmember.index', $parameters));

        $response->assertJson(
            json_decode(
                json_encode(
                    (new ArtistMemberCollection($artistMembers, new Query($parameters)))
                        ->response()
                        ->getData()
                ),
                true
            )
        );
    }

    /**
     * The Artist Member Index Endpoint shall support filtering by updated_at.
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
                ArtistMember::factory()
                    ->for(Artist::factory(), ArtistMember::RELATION_ARTIST)
                    ->for(Artist::factory(), ArtistMember::RELATION_MEMBER)
                    ->create();
            });
        });

        Carbon::withTestNow($excludedDate, function () {
            Collection::times($this->faker->randomDigitNotNull(), function () {
                ArtistMember::factory()
                    ->for(Artist::factory(), ArtistMember::RELATION_ARTIST)
                    ->for(Artist::factory(), ArtistMember::RELATION_MEMBER)
                    ->create();
            });
        });

        $artistMembers = ArtistMember::query()->where(BasePivot::ATTRIBUTE_UPDATED_AT, $updatedFilter)->get();

        $response = $this->get(route('api.artistmember.index', $parameters));

        $response->assertJson(
            json_decode(
                json_encode(
                    (new ArtistMemberCollection($artistMembers, new Query($parameters)))
                        ->response()
                        ->getData()
                ),
                true
            )
        );
    }
}
