<?php

declare(strict_types=1);

namespace Tests\Feature\Http\Api\Wiki\Audio;

use App\Contracts\Http\Api\Field\SortableField;
use App\Enums\Http\Api\Filter\TrashedStatus;
use App\Enums\Http\Api\Sort\Direction;
use App\Http\Api\Criteria\Filter\TrashedCriteria;
use App\Http\Api\Criteria\Paging\Criteria;
use App\Http\Api\Criteria\Paging\OffsetCriteria;
use App\Http\Api\Field\Field;
use App\Http\Api\Parser\FieldParser;
use App\Http\Api\Parser\FilterParser;
use App\Http\Api\Parser\PagingParser;
use App\Http\Api\Parser\SortParser;
use App\Http\Api\Query\Wiki\Audio\AudioReadQuery;
use App\Http\Api\Schema\Wiki\AudioSchema;
use App\Http\Resources\Wiki\Collection\AudioCollection;
use App\Http\Resources\Wiki\Resource\AudioResource;
use App\Models\BaseModel;
use App\Models\Wiki\Audio;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\WithoutEvents;
use Tests\TestCase;

/**
 * Class AudioIndexTest.
 */
class AudioIndexTest extends TestCase
{
    use WithFaker;
    use WithoutEvents;

    /**
     * By default, the Audio Index Endpoint shall return a collection of Audio Resources.
     *
     * @return void
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
                    (new AudioCollection($audios, new AudioReadQuery()))
                        ->response()
                        ->getData()
                ),
                true
            )
        );
    }

    /**
     * The Audio Index Endpoint shall be paginated.
     *
     * @return void
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
     * The Audio Index Endpoint shall implement sparse fieldsets.
     *
     * @return void
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
                    (new AudioCollection($audios, new AudioReadQuery($parameters)))
                        ->response()
                        ->getData()
                ),
                true
            )
        );
    }

    /**
     * The Synonym Index Endpoint shall support sorting resources.
     *
     * @return void
     */
    public function testSorts(): void
    {
        $schema = new AudioSchema();

        $sort = collect($schema->fields())
            ->filter(fn (Field $field) => $field instanceof SortableField)
            ->map(fn (SortableField $field) => $field->getSort())
            ->random();

        $parameters = [
            SortParser::param() => $sort->format(Direction::getRandomInstance()),
        ];

        $query = new AudioReadQuery($parameters);

        Audio::factory()
            ->count($this->faker->randomDigitNotNull())
            ->create();

        $response = $this->get(route('api.audio.index', $parameters));

        $response->assertJson(
            json_decode(
                json_encode(
                    $query->collection($query->index())
                        ->response()
                        ->getData()
                ),
                true
            )
        );
    }

    /**
     * The Audio Index Endpoint shall support filtering by created_at.
     *
     * @return void
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
                    (new AudioCollection($audio, new AudioReadQuery($parameters)))
                        ->response()
                        ->getData()
                ),
                true
            )
        );
    }

    /**
     * The Audio Index Endpoint shall support filtering by updated_at.
     *
     * @return void
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
                    (new AudioCollection($audio, new AudioReadQuery($parameters)))
                        ->response()
                        ->getData()
                ),
                true
            )
        );
    }

    /**
     * The Audio Index Endpoint shall support filtering by trashed.
     *
     * @return void
     */
    public function testWithoutTrashedFilter(): void
    {
        $parameters = [
            FilterParser::param() => [
                TrashedCriteria::PARAM_VALUE => TrashedStatus::WITHOUT,
            ],
            PagingParser::param() => [
                OffsetCriteria::SIZE_PARAM => Criteria::MAX_RESULTS,
            ],
        ];

        Audio::factory()->count($this->faker->randomDigitNotNull())->create();

        $deleteAudio = Audio::factory()->count($this->faker->randomDigitNotNull())->create();
        $deleteAudio->each(function (Audio $audio) {
            $audio->delete();
        });

        $audio = Audio::withoutTrashed()->get();

        $response = $this->get(route('api.audio.index', $parameters));

        $response->assertJson(
            json_decode(
                json_encode(
                    (new AudioCollection($audio, new AudioReadQuery($parameters)))
                        ->response()
                        ->getData()
                ),
                true
            )
        );
    }

    /**
     * The Audio Index Endpoint shall support filtering by trashed.
     *
     * @return void
     */
    public function testWithTrashedFilter(): void
    {
        $parameters = [
            FilterParser::param() => [
                TrashedCriteria::PARAM_VALUE => TrashedStatus::WITH,
            ],
            PagingParser::param() => [
                OffsetCriteria::SIZE_PARAM => Criteria::MAX_RESULTS,
            ],
        ];

        Audio::factory()->count($this->faker->randomDigitNotNull())->create();

        $deleteAudio = Audio::factory()->count($this->faker->randomDigitNotNull())->create();
        $deleteAudio->each(function (Audio $audio) {
            $audio->delete();
        });

        $audio = Audio::withTrashed()->get();

        $response = $this->get(route('api.audio.index', $parameters));

        $response->assertJson(
            json_decode(
                json_encode(
                    (new AudioCollection($audio, new AudioReadQuery($parameters)))
                        ->response()
                        ->getData()
                ),
                true
            )
        );
    }

    /**
     * The Audio Index Endpoint shall support filtering by trashed.
     *
     * @return void
     */
    public function testOnlyTrashedFilter(): void
    {
        $parameters = [
            FilterParser::param() => [
                TrashedCriteria::PARAM_VALUE => TrashedStatus::ONLY,
            ],
            PagingParser::param() => [
                OffsetCriteria::SIZE_PARAM => Criteria::MAX_RESULTS,
            ],
        ];

        Audio::factory()->count($this->faker->randomDigitNotNull())->create();

        $deleteAudio = Audio::factory()->count($this->faker->randomDigitNotNull())->create();
        $deleteAudio->each(function (Audio $audio) {
            $audio->delete();
        });

        $audio = Audio::onlyTrashed()->get();

        $response = $this->get(route('api.audio.index', $parameters));

        $response->assertJson(
            json_decode(
                json_encode(
                    (new AudioCollection($audio, new AudioReadQuery($parameters)))
                        ->response()
                        ->getData()
                ),
                true
            )
        );
    }

    /**
     * The Audio Index Endpoint shall support filtering by deleted_at.
     *
     * @return void
     */
    public function testDeletedAtFilter(): void
    {
        $deletedFilter = $this->faker->date();
        $excludedDate = $this->faker->date();

        $parameters = [
            FilterParser::param() => [
                BaseModel::ATTRIBUTE_DELETED_AT => $deletedFilter,
                TrashedCriteria::PARAM_VALUE => TrashedStatus::WITH,
            ],
            PagingParser::param() => [
                OffsetCriteria::SIZE_PARAM => Criteria::MAX_RESULTS,
            ],
        ];

        Carbon::withTestNow($deletedFilter, function () {
            $audios = Audio::factory()->count($this->faker->randomDigitNotNull())->create();
            $audios->each(function (Audio $audio) {
                $audio->delete();
            });
        });

        Carbon::withTestNow($excludedDate, function () {
            $audios = Audio::factory()->count($this->faker->randomDigitNotNull())->create();
            $audios->each(function (Audio $audio) {
                $audio->delete();
            });
        });

        $audio = Audio::withTrashed()->where(BaseModel::ATTRIBUTE_DELETED_AT, $deletedFilter)->get();

        $response = $this->get(route('api.audio.index', $parameters));

        $response->assertJson(
            json_decode(
                json_encode(
                    (new AudioCollection($audio, new AudioReadQuery($parameters)))
                        ->response()
                        ->getData()
                ),
                true
            )
        );
    }
}
