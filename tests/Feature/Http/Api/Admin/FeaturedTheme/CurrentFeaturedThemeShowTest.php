<?php

declare(strict_types=1);

namespace Tests\Feature\Http\Api\Admin\FeaturedTheme;

use App\Http\Api\Field\Field;
use App\Http\Api\Include\AllowedInclude;
use App\Http\Api\Parser\FieldParser;
use App\Http\Api\Parser\IncludeParser;
use App\Http\Api\Query\Query;
use App\Http\Api\Schema\Admin\FeaturedThemeSchema;
use App\Http\Resources\Admin\Resource\FeaturedThemeResource;
use App\Models\Admin\FeaturedTheme;
use App\Models\Auth\User;
use App\Models\Wiki\Anime;
use App\Models\Wiki\Anime\AnimeTheme;
use App\Models\Wiki\Anime\Theme\AnimeThemeEntry;
use App\Models\Wiki\Artist;
use App\Models\Wiki\Image;
use App\Models\Wiki\Song;
use App\Models\Wiki\Video;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Collection;
use Tests\TestCase;

/**
 * Class CurrentFeaturedThemeShowTest.
 */
class CurrentFeaturedThemeShowTest extends TestCase
{
    use WithFaker;

    /**
     * The Current Featured Theme Show Endpoint shall return a Not Found exception if there are no featured themes.
     *
     * @return void
     */
    public function testNotFoundIfNoFeaturedThemes(): void
    {
        $response = $this->get(route('api.featuredtheme.current.show'));

        $response->assertNotFound();
    }

    /**
     * The Current Featured Theme Show Endpoint shall return a Not Found exception if the featured theme has no start date.
     *
     * @return void
     */
    public function testNotFoundIfThemeStartAtNull(): void
    {
        FeaturedTheme::factory()->create([
            FeaturedTheme::ATTRIBUTE_START_AT => null,
        ]);

        $response = $this->get(route('api.featuredtheme.current.show'));

        $response->assertNotFound();
    }

    /**
     * The Current Featured Theme Show Endpoint shall return a Not Found exception if the featured theme has no end date.
     *
     * @return void
     */
    public function testNotFoundIfThemeEndAtNull(): void
    {
        FeaturedTheme::factory()->create([
            FeaturedTheme::ATTRIBUTE_END_AT => null,
        ]);

        $response = $this->get(route('api.featuredtheme.current.show'));

        $response->assertNotFound();
    }

    /**
     * The Current Featured Theme Show Endpoint shall return a Not Found exception if the featured theme starts after today.
     *
     * @return void
     */
    public function testNotFoundIfThemeStartAtAfterNow(): void
    {
        FeaturedTheme::factory()->create([
            FeaturedTheme::ATTRIBUTE_START_AT => $this->faker->dateTimeBetween('+1 day', '+1 year'),
        ]);

        $response = $this->get(route('api.featuredtheme.current.show'));

        $response->assertNotFound();
    }

    /**
     * The Current Featured Theme Show Endpoint shall return a Not Found exception if the featured theme ends before today.
     *
     * @return void
     */
    public function testNotFoundIfThemeEndAtBeforeNow(): void
    {
        FeaturedTheme::factory()->create([
            FeaturedTheme::ATTRIBUTE_END_AT => $this->faker->dateTimeBetween(),
        ]);

        $response = $this->get(route('api.featuredtheme.current.show'));

        $response->assertNotFound();
    }

    /**
     * By default, the Current Featured Theme Show Endpoint shall return a Featured Theme Resource.
     *
     * @return void
     */
    public function testDefault(): void
    {
        Collection::times($this->faker->randomDigitNotNull(), function () {
            FeaturedTheme::factory()->create([
                FeaturedTheme::ATTRIBUTE_START_AT => null,
            ]);
        });

        Collection::times($this->faker->randomDigitNotNull(), function () {
            FeaturedTheme::factory()->create([
                FeaturedTheme::ATTRIBUTE_END_AT => null,
            ]);
        });

        Collection::times($this->faker->randomDigitNotNull(), function () {
            FeaturedTheme::factory()->create([
                FeaturedTheme::ATTRIBUTE_START_AT => $this->faker->dateTimeBetween('+1 day', '+1 year'),
            ]);
        });

        Collection::times($this->faker->randomDigitNotNull(), function () {
            FeaturedTheme::factory()->create([
                FeaturedTheme::ATTRIBUTE_END_AT => $this->faker->dateTimeBetween('-1 year', '-1 day'),
            ]);
        });

        $currentTheme = FeaturedTheme::factory()->create();

        $response = $this->get(route('api.featuredtheme.current.show'));

        $response->assertJson(
            json_decode(
                json_encode(
                    (new FeaturedThemeResource($currentTheme, new Query()))
                        ->response()
                        ->getData()
                ),
                true
            )
        );
    }

    /**
     * The Current Featured Theme Show Endpoint shall return a Not Found exception if the featured theme is soft deleted.
     *
     * @return void
     */
    public function testSoftDelete(): void
    {
        FeaturedTheme::factory()->trashed()->create();

        $response = $this->get(route('api.featuredtheme.current.show'));

        $response->assertNotFound();
    }

    /**
     * The Featured Theme Show Endpoint shall allow inclusion of related resources.
     *
     * @return void
     */
    public function testAllowedIncludePaths(): void
    {
        $schema = new FeaturedThemeSchema();

        $allowedIncludes = collect($schema->allowedIncludes());

        $selectedIncludes = $allowedIncludes->random($this->faker->numberBetween(1, $allowedIncludes->count()));

        $includedPaths = $selectedIncludes->map(fn (AllowedInclude $include) => $include->path());

        $parameters = [
            IncludeParser::param() => $includedPaths->join(','),
        ];

        $currentTheme = FeaturedTheme::factory()
            ->for(
                AnimeThemeEntry::factory()
                    ->for(
                        AnimeTheme::factory()
                            ->for(Anime::factory()->has(Image::factory()->count($this->faker->randomDigitNotNull())))
                            ->for(Song::factory()->has(Artist::factory()->count($this->faker->randomDigitNotNull())))
                    )
            )
            ->for(Video::factory())
            ->for(User::factory())
            ->createOne();

        $response = $this->get(route('api.featuredtheme.current.show', $parameters));

        $response->assertJson(
            json_decode(
                json_encode(
                    (new FeaturedThemeResource($currentTheme, new Query($parameters)))
                        ->response()
                        ->getData()
                ),
                true
            )
        );
    }

    /**
     * The Current Featured Theme Show Endpoint shall implement sparse fieldsets.
     *
     * @return void
     */
    public function testSparseFieldsets(): void
    {
        $schema = new FeaturedThemeSchema();

        $fields = collect($schema->fields());

        $includedFields = $fields->random($this->faker->numberBetween(1, $fields->count()));

        $parameters = [
            FieldParser::param() => [
                FeaturedThemeResource::$wrap => $includedFields->map(fn (Field $field) => $field->getKey())->join(','),
            ],
        ];

        $currentTheme = FeaturedTheme::factory()->create();

        $response = $this->get(route('api.featuredtheme.current.show', $parameters));

        $response->assertJson(
            json_decode(
                json_encode(
                    (new FeaturedThemeResource($currentTheme, new Query($parameters)))
                        ->response()
                        ->getData()
                ),
                true
            )
        );
    }
}
