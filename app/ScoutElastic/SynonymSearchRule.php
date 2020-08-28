<?php

namespace App\ScoutElastic;

use ScoutElastic\SearchRule;

class SynonymSearchRule extends SearchRule
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
                        'text' => [
                            'query' => $this->builder->query,
                            'fuzziness' => 'AUTO',
                            'lenient' => true,
                            'operator' => 'AND'
                        ]
                    ]
                ],
                [
                    'wildcard' => [
                        'text' => [
                            'value' => $this->builder->query
                        ]
                    ]
                ]
            ],
            'minimum_should_match' => 1
        ];
    }
}
