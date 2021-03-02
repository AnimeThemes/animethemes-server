<?php

namespace App\Scout\Elastic;

use App\Http\Resources\SeriesCollection;
use App\Models\Series;
use ElasticScoutDriverPlus\Builders\MatchPhraseQueryBuilder;
use ElasticScoutDriverPlus\Builders\MatchQueryBuilder;

class SeriesQueryPayload extends ElasticQueryPayload
{
    /**
     * Build and execute Elasticsearch query.
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function performSearch()
    {
        $builder = Series::boolSearch()
            ->should((new MatchPhraseQueryBuilder())
                ->field('name')
                ->query($this->parser->getSearch())
            )
            ->should((new MatchQueryBuilder())
                ->field('name')
                ->query($this->parser->getSearch())
                ->operator('AND')
            )
            ->should((new MatchQueryBuilder())
                ->field('name')
                ->query($this->parser->getSearch())
                ->operator('AND')
                ->lenient(true)
                ->fuzziness('AUTO')
            )
            ->minimumShouldMatch(1)
            ->size($this->parser->getLimit())
            ->load($this->parser->getResourceIncludePaths(SeriesCollection::allowedIncludePaths(), SeriesCollection::resourceType()));

        foreach (SeriesCollection::filters() as $filterClass) {
            $filter = new $filterClass($this->parser);
            if ($filter->shouldApplyFilter()) {
                $builder = $builder->filter(['terms' => [$filter->getKey() => $filter->getFilterValues()]]);
            }
        }

        return $builder->execute()->models();
    }
}
