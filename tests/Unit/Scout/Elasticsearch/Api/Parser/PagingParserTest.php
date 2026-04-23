<?php

declare(strict_types=1);

use App\Http\Api\Criteria\Paging\LimitCriteria as BaseLimitCriteria;
use App\Http\Api\Criteria\Paging\OffsetCriteria as BaseOffsetCriteria;
use App\Scout\Elasticsearch\Api\Criteria\Paging\LimitCriteria;
use App\Scout\Elasticsearch\Api\Criteria\Paging\OffsetCriteria;
use App\Scout\Elasticsearch\Api\Parser\PagingParser;
use Illuminate\Foundation\Testing\WithFaker;

uses(WithFaker::class);

test('limit criteria', function (): void {
    $criteria = new BaseLimitCriteria(fake()->randomDigitNotNull());

    $this->assertInstanceOf(LimitCriteria::class, PagingParser::parse($criteria));
});

test('offset criteria', function (): void {
    $criteria = new BaseOffsetCriteria(fake()->randomDigitNotNull());

    $this->assertInstanceOf(OffsetCriteria::class, PagingParser::parse($criteria));
});
