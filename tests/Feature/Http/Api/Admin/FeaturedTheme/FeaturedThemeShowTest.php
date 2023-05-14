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
use Tests\TestCase;

/**
 * Class FeaturedThemeShowTest.
 */
class FeaturedThemeShowTest extends TestCase
{
    use WithFaker;

    /**
     * The Featured Theme Show Endpoint shall forbid the user from viewing a featured theme whose start date is in the future.
     *
     * @return void
     */
    public function testForbiddenIfFutureStartDate(): void
    {
        $featuredTheme = FeaturedTheme::factory()->create([
            FeaturedTheme::ATTRIBUTE_START_AT => $this->faker->dateTimeBetween('+1 day', '+30 years'),
        ]);

        $response = $this->get(route('api.featuredtheme.show', ['featuredtheme' => $featuredTheme]));

        $response->assertForbidden();
    }

    /**
     * By default, the Featured Theme Show Endpoint shall return a Featured Theme Resource.
     *
     * @return void
     */
    public function testDefault(): void
    {
        $featuredTheme = FeaturedTheme::factory()->create();

        $response = $this->get(route('api.featuredtheme.show', ['featuredtheme' => $featuredTheme]));

        $response->assertJson(
            json_decode(
                json_encode(
                    (new FeaturedThemeResource($featuredTheme, new Query()))
                        ->response()
                        ->getData()
                ),
                true
            )
        );
    }

    /**
     * The Featured Theme Show Endpoint shall return a Featured Theme Resource for soft deleted featured themes.
     *
     * @return void
     */
    public function testSoftDelete(): void
    {
        $featuredTheme = FeaturedTheme::factory()->createOne();

        $featuredTheme->delete();

        $featuredTheme->unsetRelations();

        $response = $this->get(route('api.featuredtheme.show', ['featuredtheme' => $featuredTheme]));

        $response->assertJson(
            json_decode(
                json_encode(
                    (new FeaturedThemeResource($featuredTheme, new Query()))
                        ->response()
                        ->getData()
                ),
                true
            )
        );
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

        $featuredTheme = FeaturedTheme::factory()
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

        $response = $this->get(route('api.featuredtheme.show', ['featuredtheme' => $featuredTheme] + $parameters));

        $response->assertJson(
            json_decode(
                json_encode(
                    (new FeaturedThemeResource($featuredTheme, new Query($parameters)))
                        ->response()
                        ->getData()
                ),
                true
            )
        );
    }

    /**
     * The Featured Theme Show Endpoint shall implement sparse fieldsets.
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

        $featuredTheme = FeaturedTheme::factory()->create();

        $response = $this->get(route('api.featuredtheme.show', ['featuredtheme' => $featuredTheme] + $parameters));

        $response->assertJson(
            json_decode(
                json_encode(
                    (new FeaturedThemeResource($featuredTheme, new Query($parameters)))
                        ->response()
                        ->getData()
                ),
                true
            )
        );
    }
}
