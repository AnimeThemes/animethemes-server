<?php

declare(strict_types=1);

use App\Http\Api\Criteria\Search\Criteria;
use App\Http\Api\Parser\SearchParser;

uses(Illuminate\Foundation\Testing\WithFaker::class);

test('no criteria by default', function () {
    $parameters = [];

    $this->assertEmpty(SearchParser::parse($parameters));
});

test('parse search criteria', function () {
    $parameters = [
        SearchParser::param() => fake()->word(),
    ];

    $criteria = SearchParser::parse($parameters)[0];

    $this->assertInstanceOf(Criteria::class, $criteria);
});

test('parse search criteria term', function () {
    $term = fake()->word();

    $parameters = [
        SearchParser::param() => $term,
    ];

    $criteria = SearchParser::parse($parameters)[0];

    $this->assertEquals($term, $criteria->getTerm());
});
