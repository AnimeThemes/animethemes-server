<?php

declare(strict_types=1);

namespace Tests\Feature\Http\Api\Wiki\Audio;

use App\Concerns\Actions\Http\Api\SortsModels;
use App\Constants\ModelConstants;
use App\Contracts\Http\Api\Field\SortableField;
use App\Enums\Http\Api\Filter\TrashedStatus;
use App\Enums\Http\Api\Sort\Direction;
use App\Http\Api\Criteria\Filter\TrashedCriteria;
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
use App\Http\Api\Schema\Wiki\AudioSchema;
use App\Http\Api\Sort\Sort;
use App\Http\Resources\Wiki\Collection\AudioCollection;
use App\Http\Resources\Wiki\Resource\AudioResource;
use App\Models\BaseModel;
use App\Models\Wiki\Audio;
use App\Models\Wiki\Video;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Arr;
use Illuminate\Support\Carbon;
use Tests\TestCase;

class AudioIndexTest extends TestCase
{
    use SortsModels;
    use WithFaker;

    /**
     * By default, the Audio Index Endpoint shall return a collection of Audio Resources.
     */
    public function testDefault(): void
    {
        $audios = Audio::factory()
            ->count($this->faker->randomDigitNotNull())
            ->create();

        $response = $this->get(route('api.audio.index'));

        $response->assertJson(
            json_decode(
                json_encode(
                    new AudioCollection($audios, new Query())
                        ->response()
                        ->getData()
                ),
                true
            )
        );
    }

    /**
     * The Audio Index Endpoint shall be paginated.
     */
    public function testPaginated(): void
    {
        Audio::factory()
            ->count($this->faker->randomDigitNotNull())
            ->create();

        $response = $this->get(route('api.audio.index'));

        $response->assertJsonStructure([
            AudioCollection::$wrap,
            'links',
            'meta',
        ]);
    }

    /**
     * The Audio Index Endpoint shall allow inclusion of related resources.
     */
    public function testAllowedIncludePaths(): void
    {
        $schema = new AudioSchema();

        $allowedIncludes = collect($schema->allowedIncludes());

        $selectedIncludes = $allowedIncludes->random($this->faker->numberBetween(1, $allowedIncludes->count()));

        $includedPaths = $selectedIncludes->map(fn (AllowedInclude $include) => $include->path());

        $parameters = [
            IncludeParser::param() => $includedPaths->join(','),
        ];

        Audio::factory()
            ->count($this->faker->randomDigitNotNull())
            ->has(Video::factory()->count($this->faker->randomDigitNotNull()))
            ->create();

        $audios = Audio::with($includedPaths->all())->get();

        $response = $this->get(route('api.audio.index', $parameters));

        $response->assertJson(
            json_decode(
                json_encode(
                    new AudioCollection($audios, new Query($parameters))
                        ->response()
                        ->getData()
                ),
                true
            )
        );
    }

    /**
     * The Audio Index Endpoint shall implement sparse fieldsets.
     */
    public function testSparseFieldsets(): void
    {
        $schema = new AudioSchema();

        $fields = collect($schema->fields());

        $includedFields = $fields->random($this->faker->numberBetween(1, $fields->count()));

        $parameters = [
            FieldParser::param() => [
                AudioResource::$wrap => $includedFields->map(fn (Field $field) => $field->getKey())->join(','),
            ],
        ];

        $audios = Audio::factory()
            ->count($this->faker->randomDigitNotNull())
            ->create();

        $response = $this->get(route('api.audio.index', $parameters));

        $response->assertJson(
            json_decode(
                json_encode(
                    new AudioCollection($audios, new Query($parameters))
                        ->response()
                        ->getData()
                ),
                true
            )
        );
    }

    /**
     * The Synonym Index Endpoint shall support sorting resources.
     */
    public function testSorts(): void
    {
        $schema = new AudioSchema();

        /** @var Sort $sort */
        $sort = collect($schema->fields())
            ->filter(fn (Field $field) => $field instanceof SortableField)
            ->map(fn (SortableField $field) => $field->getSort())
            ->random();

        $parameters = [
            SortParser::param() => $sort->format(Arr::random(Direction::cases())),
        ];

        $query = new Query($parameters);

        Audio::factory()
            ->count($this->faker->randomDigitNotNull())
            ->create();

        $response = $this->get(route('api.audio.index', $parameters));

        $audios = $this->sort(Audio::query(), $query, $schema)->get();

        $response->assertJson(
            json_decode(
                json_encode(
                    new AudioCollection($audios, $query)
                        ->response()
                        ->getData()
                ),
                true
            )
        );
    }

    /**
     * The Audio Index Endpoint shall support filtering by created_at.
     */
    public function testCreatedAtFilter(): void
    {
        $createdFilter = $this->faker->date();
        $excludedDate = $this->faker->date();

        $parameters = [
            FilterParser::param() => [
                BaseModel::ATTRIBUTE_CREATED_AT => $createdFilter,
            ],
            PagingParser::param() => [
                OffsetCriteria::SIZE_PARAM => Criteria::MAX_RESULTS,
            ],
        ];

        Carbon::withTestNow($createdFilter, function () {
            Audio::factory()->count($this->faker->randomDigitNotNull())->create();
        });

        Carbon::withTestNow($excludedDate, function () {
            Audio::factory()->count($this->faker->randomDigitNotNull())->create();
        });

        $audio = Audio::query()->where(BaseModel::ATTRIBUTE_CREATED_AT, $createdFilter)->get();

        $response = $this->get(route('api.audio.index', $parameters));

        $response->assertJson(
            json_decode(
                json_encode(
                    new AudioCollection($audio, new Query($parameters))
                        ->response()
                        ->getData()
                ),
                true
            )
        );
    }

    /**
     * The Audio Index Endpoint shall support filtering by updated_at.
     */
    public function testUpdatedAtFilter(): void
    {
        $updatedFilter = $this->faker->date();
        $excludedDate = $this->faker->date();

        $parameters = [
            FilterParser::param() => [
                BaseModel::ATTRIBUTE_UPDATED_AT => $updatedFilter,
            ],
            PagingParser::param() => [
                OffsetCriteria::SIZE_PARAM => Criteria::MAX_RESULTS,
            ],
        ];

        Carbon::withTestNow($updatedFilter, function () {
            Audio::factory()->count($this->faker->randomDigitNotNull())->create();
        });

        Carbon::withTestNow($excludedDate, function () {
            Audio::factory()->count($this->faker->randomDigitNotNull())->create();
        });

        $audio = Audio::query()->where(BaseModel::ATTRIBUTE_UPDATED_AT, $updatedFilter)->get();

        $response = $this->get(route('api.audio.index', $parameters));

        $response->assertJson(
            json_decode(
                json_encode(
                    new AudioCollection($audio, new Query($parameters))
                        ->response()
                        ->getData()
                ),
                true
            )
        );
    }

    /**
     * The Audio Index Endpoint shall support filtering by trashed.
     */
    public function testWithoutTrashedFilter(): void
    {
        $parameters = [
            FilterParser::param() => [
                TrashedCriteria::PARAM_VALUE => TrashedStatus::WITHOUT->value,
            ],
            PagingParser::param() => [
                OffsetCriteria::SIZE_PARAM => Criteria::MAX_RESULTS,
            ],
        ];

        Audio::factory()->count($this->faker->randomDigitNotNull())->create();

        Audio::factory()->trashed()->count($this->faker->randomDigitNotNull())->create();

        $audio = Audio::withoutTrashed()->get();

        $response = $this->get(route('api.audio.index', $parameters));

        $response->assertJson(
            json_decode(
                json_encode(
                    new AudioCollection($audio, new Query($parameters))
                        ->response()
                        ->getData()
                ),
                true
            )
        );
    }

    /**
     * The Audio Index Endpoint shall support filtering by trashed.
     */
    public function testWithTrashedFilter(): void
    {
        $parameters = [
            FilterParser::param() => [
                TrashedCriteria::PARAM_VALUE => TrashedStatus::WITH->value,
            ],
            PagingParser::param() => [
                OffsetCriteria::SIZE_PARAM => Criteria::MAX_RESULTS,
            ],
        ];

        Audio::factory()->count($this->faker->randomDigitNotNull())->create();

        Audio::factory()->trashed()->count($this->faker->randomDigitNotNull())->create();

        $audio = Audio::withTrashed()->get();

        $response = $this->get(route('api.audio.index', $parameters));

        $response->assertJson(
            json_decode(
                json_encode(
                    new AudioCollection($audio, new Query($parameters))
                        ->response()
                        ->getData()
                ),
                true
            )
        );
    }

    /**
     * The Audio Index Endpoint shall support filtering by trashed.
     */
    public function testOnlyTrashedFilter(): void
    {
        $parameters = [
            FilterParser::param() => [
                TrashedCriteria::PARAM_VALUE => TrashedStatus::ONLY->value,
            ],
            PagingParser::param() => [
                OffsetCriteria::SIZE_PARAM => Criteria::MAX_RESULTS,
            ],
        ];

        Audio::factory()->count($this->faker->randomDigitNotNull())->create();

        Audio::factory()->trashed()->count($this->faker->randomDigitNotNull())->create();

        $audio = Audio::onlyTrashed()->get();

        $response = $this->get(route('api.audio.index', $parameters));

        $response->assertJson(
            json_decode(
                json_encode(
                    new AudioCollection($audio, new Query($parameters))
                        ->response()
                        ->getData()
                ),
                true
            )
        );
    }

    /**
     * The Audio Index Endpoint shall support filtering by deleted_at.
     */
    public function testDeletedAtFilter(): void
    {
        $deletedFilter = $this->faker->date();
        $excludedDate = $this->faker->date();

        $parameters = [
            FilterParser::param() => [
                ModelConstants::ATTRIBUTE_DELETED_AT => $deletedFilter,
                TrashedCriteria::PARAM_VALUE => TrashedStatus::WITH->value,
            ],
            PagingParser::param() => [
                OffsetCriteria::SIZE_PARAM => Criteria::MAX_RESULTS,
            ],
        ];

        Carbon::withTestNow($deletedFilter, function () {
            Audio::factory()->trashed()->count($this->faker->randomDigitNotNull())->create();
        });

        Carbon::withTestNow($excludedDate, function () {
            Audio::factory()->trashed()->count($this->faker->randomDigitNotNull())->create();
        });

        $audio = Audio::withTrashed()->where(ModelConstants::ATTRIBUTE_DELETED_AT, $deletedFilter)->get();

        $response = $this->get(route('api.audio.index', $parameters));

        $response->assertJson(
            json_decode(
                json_encode(
                    new AudioCollection($audio, new Query($parameters))
                        ->response()
                        ->getData()
                ),
                true
            )
        );
    }
}
