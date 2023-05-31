<?php

declare(strict_types=1);

namespace App\Http\Requests\Api\Wiki\Anime;

use App\Http\Api\Parser\FilterParser;
use App\Http\Api\Parser\SortParser;
use App\Http\Requests\Api\ShowRequest;

/**
 * Class YearShowRequest.
 */
class YearShowRequest extends ShowRequest
{
    /**
     * Get the filter validation rules.
     *
     * @return array
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
     * @return array
     *
     * @noinspection PhpMissingParentCallCommonInspection
     */
    protected function getSortRules(): array
    {
        return $this->prohibit(SortParser::param());
    }
}
