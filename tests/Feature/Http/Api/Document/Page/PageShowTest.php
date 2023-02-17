<?php

declare(strict_types=1);

namespace Tests\Feature\Http\Api\Document\Page;

use App\Http\Api\Field\Field;
use App\Http\Api\Parser\FieldParser;
use App\Http\Api\Query\Query;
use App\Http\Api\Schema\Document\PageSchema;
use App\Http\Resources\Document\Resource\PageResource;
use App\Models\Document\Page;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\WithoutEvents;
use Tests\TestCase;

/**
 * Class PageShowTest.
 */
class PageShowTest extends TestCase
{
    use WithFaker;
    use WithoutEvents;

    /**
     * By default, the Page Show Endpoint shall return a Page Resource.
     *
     * @return void
     */
    public function testDefault(): void
    {
        $page = Page::factory()->create();

        $response = $this->get(route('api.page.show', ['page' => $page]));

        $response->assertJson(
            json_decode(
                json_encode(
                    (new PageResource($page, new Query()))
                        ->response()
                        ->getData()
                ),
                true
            )
        );
    }

    /**
     * The Page Show Endpoint shall return a Page Resource for soft deleted images.
     *
     * @return void
     */
    public function testSoftDelete(): void
    {
        $page = Page::factory()->createOne();

        $page->delete();

        $page->unsetRelations();

        $response = $this->get(route('api.page.show', ['page' => $page]));

        $response->assertJson(
            json_decode(
                json_encode(
                    (new PageResource($page, new Query()))
                        ->response()
                        ->getData()
                ),
                true
            )
        );
    }

    /**
     * The Page Show Endpoint shall implement sparse fieldsets.
     *
     * @return void
     */
    public function testSparseFieldsets(): void
    {
        $schema = new PageSchema();

        $fields = collect($schema->fields());

        $includedFields = $fields->random($this->faker->numberBetween(1, $fields->count()));

        $parameters = [
            FieldParser::param() => [
                PageResource::$wrap => $includedFields->map(fn (Field $field) => $field->getKey())->join(','),
            ],
        ];

        $page = Page::factory()->create();

        $response = $this->get(route('api.page.show', ['page' => $page] + $parameters));

        $response->assertJson(
            json_decode(
                json_encode(
                    (new PageResource($page, new Query($parameters)))
                        ->response()
                        ->getData()
                ),
                true
            )
        );
    }
}
