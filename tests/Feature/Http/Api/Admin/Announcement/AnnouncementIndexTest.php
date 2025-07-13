<?php

declare(strict_types=1);

namespace Tests\Feature\Http\Api\Admin\Announcement;

use App\Concerns\Actions\Http\Api\SortsModels;
use App\Contracts\Http\Api\Field\SortableField;
use App\Enums\Http\Api\Sort\Direction;
use App\Http\Api\Criteria\Paging\Criteria;
use App\Http\Api\Criteria\Paging\OffsetCriteria;
use App\Http\Api\Field\Field;
use App\Http\Api\Parser\FieldParser;
use App\Http\Api\Parser\FilterParser;
use App\Http\Api\Parser\PagingParser;
use App\Http\Api\Parser\SortParser;
use App\Http\Api\Query\Query;
use App\Http\Api\Schema\Admin\AnnouncementSchema;
use App\Http\Api\Sort\Sort;
use App\Http\Resources\Admin\Collection\AnnouncementCollection;
use App\Http\Resources\Admin\Resource\AnnouncementResource;
use App\Models\Admin\Announcement;
use App\Models\BaseModel;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Arr;
use Illuminate\Support\Carbon;
use Tests\TestCase;

/**
 * Class AnnouncementIndexTest.
 */
class AnnouncementIndexTest extends TestCase
{
    use SortsModels;
    use WithFaker;

    /**
     * By default, the Announcement Index Endpoint shall return a collection of Announcement Resources.
     *
     * @return void
     */
    public function testDefault(): void
    {
        $announcements = Announcement::factory()->count($this->faker->randomDigitNotNull())->create();

        $response = $this->get(route('api.announcement.index'));

        $response->assertJson(
            json_decode(
                json_encode(
                    new AnnouncementCollection($announcements, new Query())
                        ->response()
                        ->getData()
                ),
                true
            )
        );
    }

    /**
     * The Announcement Index Endpoint shall return a collection of public Announcement Resources.
     *
     * @return void
     */
    public function testPrivate(): void
    {
        Announcement::factory()
            ->private()
            ->count($this->faker->randomDigitNotNull())
            ->create();

        $announcements = Announcement::factory()
            ->count($this->faker->randomDigitNotNull())
            ->create();

        $response = $this->get(route('api.announcement.index'));

        $response->assertJson(
            json_decode(
                json_encode(
                    new AnnouncementCollection($announcements, new Query())
                        ->response()
                        ->getData()
                ),
                true
            )
        );
    }

    /**
     * The Announcement Index Endpoint shall be paginated.
     *
     * @return void
     */
    public function testPaginated(): void
    {
        Announcement::factory()->count($this->faker->randomDigitNotNull())->create();

        $response = $this->get(route('api.announcement.index'));

        $response->assertJsonStructure([
            AnnouncementCollection::$wrap,
            'links',
            'meta',
        ]);
    }

    /**
     * The Announcement Index Endpoint shall implement sparse fieldsets.
     *
     * @return void
     */
    public function testSparseFieldsets(): void
    {
        $schema = new AnnouncementSchema();

        $fields = collect($schema->fields());

        $includedFields = $fields->random($this->faker->numberBetween(1, $fields->count()));

        $parameters = [
            FieldParser::param() => [
                AnnouncementResource::$wrap => $includedFields->map(fn (Field $field) => $field->getKey())->join(','),
            ],
        ];

        $announcements = Announcement::factory()->count($this->faker->randomDigitNotNull())->create();

        $response = $this->get(route('api.announcement.index', $parameters));

        $response->assertJson(
            json_decode(
                json_encode(
                    new AnnouncementCollection($announcements, new Query($parameters))
                        ->response()
                        ->getData()
                ),
                true
            )
        );
    }

    /**
     * The Announcement Index Endpoint shall support sorting resources.
     *
     * @return void
     */
    public function testSorts(): void
    {
        $schema = new AnnouncementSchema();

        /** @var Sort $sort */
        $sort = collect($schema->fields())
            ->filter(fn (Field $field) => $field instanceof SortableField)
            ->map(fn (SortableField $field) => $field->getSort())
            ->random();

        $parameters = [
            SortParser::param() => $sort->format(Arr::random(Direction::cases())),
        ];

        $query = new Query($parameters);

        Announcement::factory()->count($this->faker->randomDigitNotNull())->create();

        $response = $this->get(route('api.announcement.index', $parameters));

        $announcements = $this->sort(Announcement::query(), $query, $schema)->get();

        $response->assertJson(
            json_decode(
                json_encode(
                    new AnnouncementCollection($announcements, $query)
                        ->response()
                        ->getData()
                ),
                true
            )
        );
    }

    /**
     * The Announcement Index Endpoint shall support filtering by created_at.
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
            Announcement::factory()->count($this->faker->randomDigitNotNull())->create();
        });

        Carbon::withTestNow($excludedDate, function () {
            Announcement::factory()->count($this->faker->randomDigitNotNull())->create();
        });

        $announcement = Announcement::query()->where(BaseModel::ATTRIBUTE_CREATED_AT, $createdFilter)->get();

        $response = $this->get(route('api.announcement.index', $parameters));

        $response->assertJson(
            json_decode(
                json_encode(
                    new AnnouncementCollection($announcement, new Query($parameters))
                        ->response()
                        ->getData()
                ),
                true
            )
        );
    }

    /**
     * The Announcement Index Endpoint shall support filtering by updated_at.
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
            Announcement::factory()->count($this->faker->randomDigitNotNull())->create();
        });

        Carbon::withTestNow($excludedDate, function () {
            Announcement::factory()->count($this->faker->randomDigitNotNull())->create();
        });

        $announcement = Announcement::query()->where(BaseModel::ATTRIBUTE_UPDATED_AT, $updatedFilter)->get();

        $response = $this->get(route('api.announcement.index', $parameters));

        $response->assertJson(
            json_decode(
                json_encode(
                    new AnnouncementCollection($announcement, new Query($parameters))
                        ->response()
                        ->getData()
                ),
                true
            )
        );
    }
}
