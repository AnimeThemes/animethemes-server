<?php

declare(strict_types=1);

use App\Http\Api\Parser\SearchParser;
use App\Scout\Criteria;
use Illuminate\Foundation\Testing\WithFaker;

uses(WithFaker::class);

test('no criteria by default', function (): void {
    $parameters = [];

    $this->assertEmpty(SearchParser::parse($parameters));
});

test('parse search criteria', function (): void {
    $parameters = [
        SearchParser::param() => fake()->word(),
    ];

    $criteria = SearchParser::parse($parameters)[0];

    $this->assertInstanceOf(Criteria::class, $criteria);
});

test('parse search criteria term', function (): void {
    $term = fake()->word();

    $parameters = [
        SearchParser::param() => $term,
    ];

    $criteria = SearchParser::parse($parameters)[0];

    $this->assertEquals($term, $criteria->getTerm());
});
