<?php

declare(strict_types=1);

namespace App\Scout\Elasticsearch\Api\Criteria\Paging;

use App\Http\Api\Criteria\Paging\OffsetCriteria as BaseCriteria;
use App\Http\Api\Parser\PagingParser;
use Elastic\ScoutDriverPlus\Builders\SearchParametersBuilder;
use Elastic\ScoutDriverPlus\Paginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Str;

class OffsetCriteria extends Criteria
{
    public function __construct(BaseCriteria $criteria)
    {
        parent::__construct($criteria);
    }

    public function paginate(SearchParametersBuilder $builder): Collection|Paginator
    {
        $pageNameQuery = Str::of(PagingParser::param())
            ->append('.')
            ->append(BaseCriteria::NUMBER_PARAM)
            ->__toString();

        $pageNameLink = Str::of(PagingParser::param())
            ->append('[')
            ->append(BaseCriteria::NUMBER_PARAM)
            ->append(']')
            ->__toString();

        $paginator = $builder->paginate($this->criteria->getResultSize(), $pageNameQuery)
            ->setPageName($pageNameLink)
            ->appends(Request::except($pageNameQuery));

        $paginator->setCollection($paginator->models());

        return $paginator;
    }
}
