<?php

declare(strict_types=1);

use App\Http\Api\Parser\SearchParser;
use App\Scout\Criteria;
use Illuminate\Foundation\Testing\WithFaker;

pest()->use(WithFaker::class);

test('no criteria by default', function (): void {
    $parameters = [];

    expect(SearchParser::parse($parameters))->toBeEmpty();
});

test('parse search criteria', function (): void {
    $parameters = [
        SearchParser::param() => fake()->word(),
    ];

    $criteria = SearchParser::parse($parameters)[0];

    expect($criteria)->toBeInstanceOf(Criteria::class);
});

test('parse search criteria term', function (): void {
    $term = fake()->word();

    $parameters = [
        SearchParser::param() => $term,
    ];

    $criteria = SearchParser::parse($parameters)[0];

    expect($criteria->getTerm())->toEqual($term);
});
