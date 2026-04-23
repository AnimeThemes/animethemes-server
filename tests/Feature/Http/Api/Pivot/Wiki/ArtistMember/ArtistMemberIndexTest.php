<?php

declare(strict_types=1);

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
use App\Http\Resources\Pivot\Wiki\Resource\ArtistMemberJsonResource;
use App\Models\Wiki\Artist;
use App\Pivots\BasePivot;
use App\Pivots\Wiki\ArtistMember;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Date;

use function Pest\Laravel\get;

uses(SortsModels::class);

uses(WithFaker::class);

test('default', function (): void {
    Collection::times(fake()->randomDigitNotNull(), function (): void {
        ArtistMember::factory()
            ->for(Artist::factory(), ArtistMember::RELATION_ARTIST)
            ->for(Artist::factory(), ArtistMember::RELATION_MEMBER)
            ->create();
    });

    $artistMembers = ArtistMember::all();

    $response = get(route('api.artistmember.index'));

    $response->assertJson(
        json_decode(
            json_encode(
                new ArtistMemberCollection($artistMembers, new Query())
                    ->response()
                    ->getData()
            ),
            true
        )
    );
});

test('paginated', function (): void {
    Collection::times(fake()->randomDigitNotNull(), function (): void {
        ArtistMember::factory()
            ->for(Artist::factory(), ArtistMember::RELATION_ARTIST)
            ->for(Artist::factory(), ArtistMember::RELATION_MEMBER)
            ->create();
    });

    $response = get(route('api.artistmember.index'));

    $response->assertJsonStructure([
        ArtistMemberCollection::$wrap,
        'links',
        'meta',
    ]);
});

test('allowed include paths', function (): void {
    $schema = new ArtistMemberSchema();

    $allowedIncludes = collect($schema->allowedIncludes());

    $selectedIncludes = $allowedIncludes->random(fake()->numberBetween(1, $allowedIncludes->count()));

    $includedPaths = $selectedIncludes->map(fn (AllowedInclude $include): string => $include->path());

    $parameters = [
        IncludeParser::param() => $includedPaths->join(','),
    ];

    Collection::times(fake()->randomDigitNotNull(), function (): void {
        ArtistMember::factory()
            ->for(Artist::factory(), ArtistMember::RELATION_ARTIST)
            ->for(Artist::factory(), ArtistMember::RELATION_MEMBER)
            ->create();
    });

    $response = get(route('api.artistmember.index', $parameters));

    $artistMembers = ArtistMember::with($includedPaths->all())->get();

    $response->assertJson(
        json_decode(
            json_encode(
                new ArtistMemberCollection($artistMembers, new Query($parameters))
                    ->response()
                    ->getData()
            ),
            true
        )
    );
});

test('sparse fieldsets', function (): void {
    $schema = new ArtistMemberSchema();

    $fields = collect($schema->fields());

    $includedFields = $fields->random(fake()->numberBetween(1, $fields->count()));

    $parameters = [
        FieldParser::param() => [
            ArtistMemberJsonResource::$wrap => $includedFields->map(fn (Field $field): string => $field->getKey())->join(','),
        ],
    ];

    Collection::times(fake()->randomDigitNotNull(), function (): void {
        ArtistMember::factory()
            ->for(Artist::factory(), ArtistMember::RELATION_ARTIST)
            ->for(Artist::factory(), ArtistMember::RELATION_MEMBER)
            ->create();
    });

    $response = get(route('api.artistmember.index', $parameters));

    $artistMembers = ArtistMember::all();

    $response->assertJson(
        json_decode(
            json_encode(
                new ArtistMemberCollection($artistMembers, new Query($parameters))
                    ->response()
                    ->getData()
            ),
            true
        )
    );
});

test('sorts', function (): void {
    $schema = new ArtistMemberSchema();

    /** @var Sort $sort */
    $sort = collect($schema->fields())
        ->filter(fn (Field $field): bool => $field instanceof SortableField)
        ->map(fn (SortableField $field): Sort => $field->getSort())
        ->random();

    $parameters = [
        SortParser::param() => $sort->format(Arr::random(Direction::cases())),
    ];

    $query = new Query($parameters);

    Collection::times(fake()->randomDigitNotNull(), function (): void {
        ArtistMember::factory()
            ->for(Artist::factory(), ArtistMember::RELATION_ARTIST)
            ->for(Artist::factory(), ArtistMember::RELATION_MEMBER)
            ->create();
    });

    $response = get(route('api.artistmember.index', $parameters));

    $artistMembers = $this->sort(ArtistMember::query(), $query, $schema)->get();

    $response->assertJson(
        json_decode(
            json_encode(
                new ArtistMemberCollection($artistMembers, $query)
                    ->response()
                    ->getData()
            ),
            true
        )
    );
});

test('created at filter', function (): void {
    $createdFilter = fake()->date();
    $excludedDate = fake()->date();

    $parameters = [
        FilterParser::param() => [
            BasePivot::ATTRIBUTE_CREATED_AT => $createdFilter,
        ],
        PagingParser::param() => [
            OffsetCriteria::SIZE_PARAM => Criteria::MAX_RESULTS,
        ],
    ];

    Date::withTestNow($createdFilter, function (): void {
        Collection::times(fake()->randomDigitNotNull(), function (): void {
            ArtistMember::factory()
                ->for(Artist::factory(), ArtistMember::RELATION_ARTIST)
                ->for(Artist::factory(), ArtistMember::RELATION_MEMBER)
                ->create();
        });
    });

    Date::withTestNow($excludedDate, function (): void {
        Collection::times(fake()->randomDigitNotNull(), function (): void {
            ArtistMember::factory()
                ->for(Artist::factory(), ArtistMember::RELATION_ARTIST)
                ->for(Artist::factory(), ArtistMember::RELATION_MEMBER)
                ->create();
        });
    });

    $artistMembers = ArtistMember::query()->where(BasePivot::ATTRIBUTE_CREATED_AT, $createdFilter)->get();

    $response = get(route('api.artistmember.index', $parameters));

    $response->assertJson(
        json_decode(
            json_encode(
                new ArtistMemberCollection($artistMembers, new Query($parameters))
                    ->response()
                    ->getData()
            ),
            true
        )
    );
});

test('updated at filter', function (): void {
    $updatedFilter = fake()->date();
    $excludedDate = fake()->date();

    $parameters = [
        FilterParser::param() => [
            BasePivot::ATTRIBUTE_UPDATED_AT => $updatedFilter,
        ],
        PagingParser::param() => [
            OffsetCriteria::SIZE_PARAM => Criteria::MAX_RESULTS,
        ],
    ];

    Date::withTestNow($updatedFilter, function (): void {
        Collection::times(fake()->randomDigitNotNull(), function (): void {
            ArtistMember::factory()
                ->for(Artist::factory(), ArtistMember::RELATION_ARTIST)
                ->for(Artist::factory(), ArtistMember::RELATION_MEMBER)
                ->create();
        });
    });

    Date::withTestNow($excludedDate, function (): void {
        Collection::times(fake()->randomDigitNotNull(), function (): void {
            ArtistMember::factory()
                ->for(Artist::factory(), ArtistMember::RELATION_ARTIST)
                ->for(Artist::factory(), ArtistMember::RELATION_MEMBER)
                ->create();
        });
    });

    $artistMembers = ArtistMember::query()->where(BasePivot::ATTRIBUTE_UPDATED_AT, $updatedFilter)->get();

    $response = get(route('api.artistmember.index', $parameters));

    $response->assertJson(
        json_decode(
            json_encode(
                new ArtistMemberCollection($artistMembers, new Query($parameters))
                    ->response()
                    ->getData()
            ),
            true
        )
    );
});
