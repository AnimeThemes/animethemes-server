<?php

namespace Tests\Feature\Http\Api\Anime;

use App\Enums\AnimeSeason;
use App\Http\Resources\AnimeCollection;
use App\Http\Resources\AnimeResource;
use App\JsonApi\QueryParser;
use App\Models\Anime;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Str;
use Tests\TestCase;

class YearShowTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    /**
     * By default, the Year Show Endpoint shall return a grouping of Anime Resources of year by season.
     *
     * @return void
     */
    public function testDefault()
    {
        $this->withoutEvents();

        $year = intval($this->faker->year());

        Anime::factory()
            ->count($this->faker->numberBetween(1, 3))
            ->create([
                'year' => $year,
                'season' => AnimeSeason::WINTER,
            ]);

        $winter_anime = Anime::where('season', AnimeSeason::WINTER)->get();
        $winter_resources = AnimeCollection::make($winter_anime->sortBy('name')->values(), QueryParser::make());

        Anime::factory()
            ->count($this->faker->numberBetween(1, 3))
            ->create([
                'year' => $year,
                'season' => AnimeSeason::SPRING,
            ]);

        $spring_anime = Anime::where('season', AnimeSeason::SPRING)->get();
        $spring_resources = AnimeCollection::make($spring_anime->sortBy('name')->values(), QueryParser::make());

        Anime::factory()
            ->count($this->faker->numberBetween(1, 3))
            ->create([
                'year' => $year,
                'season' => AnimeSeason::SUMMER,
            ]);

        $summer_anime = Anime::where('season', AnimeSeason::SUMMER)->get();
        $summer_resources = AnimeCollection::make($summer_anime->sortBy('name')->values(), QueryParser::make());

        Anime::factory()
            ->count($this->faker->numberBetween(1, 3))
            ->create([
                'year' => $year,
                'season' => AnimeSeason::FALL,
            ]);

        $fall_anime = Anime::where('season', AnimeSeason::FALL)->get();
        $fall_resources = AnimeCollection::make($fall_anime->sortBy('name')->values(), QueryParser::make());

        $response = $this->get(route('api.year.show', ['year' => $year]));

        $response->assertJson([
            Str::lower(AnimeSeason::getDescription(AnimeSeason::WINTER)) => json_decode(json_encode($winter_resources->response()->getData()->anime), true),
            Str::lower(AnimeSeason::getDescription(AnimeSeason::SPRING)) => json_decode(json_encode($spring_resources->response()->getData()->anime), true),
            Str::lower(AnimeSeason::getDescription(AnimeSeason::SUMMER)) => json_decode(json_encode($summer_resources->response()->getData()->anime), true),
            Str::lower(AnimeSeason::getDescription(AnimeSeason::FALL)) => json_decode(json_encode($fall_resources->response()->getData()->anime), true),
        ]);
    }

    /**
     * The Year Show Endpoint shall allow inclusion of related resources.
     *
     * @return void
     */
    public function testAllowedIncludePaths()
    {
        $year = intval($this->faker->year());

        $allowed_paths = collect(AnimeCollection::allowedIncludePaths());
        $included_paths = $allowed_paths->random($this->faker->numberBetween(0, count($allowed_paths)));

        $parameters = [
            QueryParser::PARAM_INCLUDE => $included_paths->join(','),
        ];

        Anime::factory()
            ->count($this->faker->numberBetween(1, 3))
            ->jsonApiResource()
            ->create([
                'year' => $year,
                'season' => AnimeSeason::WINTER,
            ]);

        $winter_anime = Anime::where('season', AnimeSeason::WINTER)->with($included_paths->all())->get();
        $winter_resources = AnimeCollection::make($winter_anime->sortBy('name')->values(), QueryParser::make($parameters));

        Anime::factory()
            ->count($this->faker->numberBetween(1, 3))
            ->jsonApiResource()
            ->create([
                'year' => $year,
                'season' => AnimeSeason::SPRING,
            ]);

        $spring_anime = Anime::where('season', AnimeSeason::SPRING)->with($included_paths->all())->get();
        $spring_resources = AnimeCollection::make($spring_anime->sortBy('name')->values(), QueryParser::make($parameters));

        Anime::factory()
            ->count($this->faker->numberBetween(1, 3))
            ->jsonApiResource()
            ->create([
                'year' => $year,
                'season' => AnimeSeason::SUMMER,
            ]);

        $summer_anime = Anime::where('season', AnimeSeason::SUMMER)->with($included_paths->all())->get();
        $summer_resources = AnimeCollection::make($summer_anime->sortBy('name')->values(), QueryParser::make($parameters));

        Anime::factory()
            ->count($this->faker->numberBetween(1, 3))
            ->jsonApiResource()
            ->create([
                'year' => $year,
                'season' => AnimeSeason::FALL,
            ]);

        $fall_anime = Anime::where('season', AnimeSeason::FALL)->with($included_paths->all())->get();
        $fall_resources = AnimeCollection::make($fall_anime->sortBy('name')->values(), QueryParser::make($parameters));

        $response = $this->get(route('api.year.show', ['year' => $year] + $parameters));

        $response->assertJson([
            Str::lower(AnimeSeason::getDescription(AnimeSeason::WINTER)) => json_decode(json_encode($winter_resources->response()->getData()->anime), true),
            Str::lower(AnimeSeason::getDescription(AnimeSeason::SPRING)) => json_decode(json_encode($spring_resources->response()->getData()->anime), true),
            Str::lower(AnimeSeason::getDescription(AnimeSeason::SUMMER)) => json_decode(json_encode($summer_resources->response()->getData()->anime), true),
            Str::lower(AnimeSeason::getDescription(AnimeSeason::FALL)) => json_decode(json_encode($fall_resources->response()->getData()->anime), true),
        ]);
    }

    /**
     * The Year Show Endpoint shall implement sparse fieldsets.
     *
     * @return void
     */
    public function testSparseFieldsets()
    {
        $this->withoutEvents();

        $year = intval($this->faker->year());

        $fields = collect([
            'id',
            'name',
            'slug',
            'year',
            'season',
            'synopsis',
            'created_at',
            'updated_at',
            'deleted_at',
        ]);

        $included_fields = $fields->random($this->faker->numberBetween(0, count($fields)));

        $parameters = [
            QueryParser::PARAM_FIELDS => [
                AnimeResource::$wrap => $included_fields->join(','),
            ],
        ];

        Anime::factory()
            ->count($this->faker->numberBetween(1, 3))
            ->create([
                'year' => $year,
                'season' => AnimeSeason::WINTER,
            ]);

        $winter_anime = Anime::where('season', AnimeSeason::WINTER)->get();
        $winter_resources = AnimeCollection::make($winter_anime->sortBy('name')->values(), QueryParser::make($parameters));

        Anime::factory()
            ->count($this->faker->numberBetween(1, 3))
            ->create([
                'year' => $year,
                'season' => AnimeSeason::SPRING,
            ]);

        $spring_anime = Anime::where('season', AnimeSeason::SPRING)->get();
        $spring_resources = AnimeCollection::make($spring_anime->sortBy('name')->values(), QueryParser::make($parameters));

        Anime::factory()
            ->count($this->faker->numberBetween(1, 3))
            ->create([
                'year' => $year,
                'season' => AnimeSeason::SUMMER,
            ]);

        $summer_anime = Anime::where('season', AnimeSeason::SUMMER)->get();
        $summer_resources = AnimeCollection::make($summer_anime->sortBy('name')->values(), QueryParser::make($parameters));

        Anime::factory()
            ->count($this->faker->numberBetween(1, 3))
            ->create([
                'year' => $year,
                'season' => AnimeSeason::FALL,
            ]);

        $fall_anime = Anime::where('season', AnimeSeason::FALL)->get();
        $fall_resources = AnimeCollection::make($fall_anime->sortBy('name')->values(), QueryParser::make($parameters));

        $response = $this->get(route('api.year.show', ['year' => $year]));

        $response->assertJson([
            Str::lower(AnimeSeason::getDescription(AnimeSeason::WINTER)) => json_decode(json_encode($winter_resources->response()->getData()->anime), true),
            Str::lower(AnimeSeason::getDescription(AnimeSeason::SPRING)) => json_decode(json_encode($spring_resources->response()->getData()->anime), true),
            Str::lower(AnimeSeason::getDescription(AnimeSeason::SUMMER)) => json_decode(json_encode($summer_resources->response()->getData()->anime), true),
            Str::lower(AnimeSeason::getDescription(AnimeSeason::FALL)) => json_decode(json_encode($fall_resources->response()->getData()->anime), true),
        ]);
    }
}
