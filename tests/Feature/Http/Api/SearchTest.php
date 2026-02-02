<?php

declare(strict_types=1);

use App\Http\Api\Parser\FieldParser;
use App\Http\Api\Parser\SearchParser;
use App\Http\Resources\List\Collection\PlaylistCollection;
use App\Http\Resources\SearchJsonResource;
use App\Http\Resources\Wiki\Anime\Collection\ThemeCollection;
use App\Http\Resources\Wiki\Collection\AnimeCollection;
use App\Http\Resources\Wiki\Collection\ArtistCollection;
use App\Http\Resources\Wiki\Collection\SeriesCollection;
use App\Http\Resources\Wiki\Collection\SongCollection;
use App\Http\Resources\Wiki\Collection\StudioCollection;
use App\Http\Resources\Wiki\Collection\VideoCollection;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Config;

use function Pest\Laravel\get;

uses(Illuminate\Foundation\Testing\WithFaker::class);

test('no search term', function () {
    $response = get(route('api.search.show'));

    $response->assertJsonValidationErrors(SearchParser::param());
});

test('search attributes', function () {
    $driver = Config::get('scout.driver');
    if (empty($driver)) {
        $this->markTestSkipped('A driver must be configured for this test');
    }

    $q = fake()->word();

    $parameters = [
        SearchParser::param() => $q,
    ];

    $response = get(route('api.search.show', $parameters));

    $response->assertJson([
        SearchJsonResource::$wrap => [
            AnimeCollection::$wrap => [],
            ThemeCollection::$wrap => [],
            ArtistCollection::$wrap => [],
            PlaylistCollection::$wrap => [],
            SeriesCollection::$wrap => [],
            SongCollection::$wrap => [],
            StudioCollection::$wrap => [],
            VideoCollection::$wrap => [],
        ],
    ]);
});

test('search sparse fieldsets', function () {
    $driver = Config::get('scout.driver');
    if (empty($driver)) {
        $this->markTestSkipped('A driver must be configured for this test');
    }

    $fields = [
        AnimeCollection::$wrap,
        ThemeCollection::$wrap,
        ArtistCollection::$wrap,
        PlaylistCollection::$wrap,
        SeriesCollection::$wrap,
        SongCollection::$wrap,
        StudioCollection::$wrap,
        VideoCollection::$wrap,
    ];

    $includedFields = Arr::random($fields, fake()->numberBetween(1, count($fields)));

    $q = fake()->word();

    $parameters = [
        SearchParser::param() => $q,
        FieldParser::param() => [
            SearchJsonResource::$wrap => implode(',', $includedFields),
        ],
    ];

    $response = get(route('api.search.show', $parameters));

    $response->assertJsonStructure([
        SearchJsonResource::$wrap => $includedFields,
    ]);
});
