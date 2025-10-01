<?php

declare(strict_types=1);

namespace App\Http\Requests\Api\List\Playlist;

use App\Http\Api\Parser\FilterParser;
use App\Http\Api\Parser\SortParser;
use App\Http\Requests\Api\IndexRequest;

class ForwardBackwardIndexRequest extends IndexRequest
{
    /**
     * Get the filter validation rules.
     *
     *
     * @noinspection PhpMissingParentCallCommonInspection
     */
    protected function getFilterRules(): array
    {
        return $this->prohibit(FilterParser::param());
    }

    /**
     * Get the sort validation rules.
     *
     *
     * @noinspection PhpMissingParentCallCommonInspection
     */
    protected function getSortRules(): array
    {
        return $this->prohibit(SortParser::param());
    }
}
