<?php

namespace App\ScoutElastic;

use ScoutElastic\SearchRule;

class SeriesSearchRule extends SearchRule
{
    /**
     * @inheritdoc
     */
    public function buildHighlightPayload()
    {
        //
    }

    /**
     * @inheritdoc
     */
    public function buildQueryPayload()
    {
        return [
            'should' => [
                [
                    'match' => [
                        'name' => [
                            'query' => $this->builder->query,
                            'fuzziness' => 'AUTO',
                            'lenient' => true,
                            'operator' => 'AND'
                        ]
                    ]
                ],
                [
                    'wildcard' => [
                        'name' => [
                            'value' => $this->builder->query
                        ]
                    ]
                ]
            ],
            'minimum_should_match' => 1
        ];
    }
}
