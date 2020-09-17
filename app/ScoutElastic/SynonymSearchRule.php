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
        return null;
    }

    /**
     * @inheritdoc
     */
    public function buildQueryPayload()
    {
        return [
            'should' => [
                [
                    'match_phrase' => [
                        'text' => [
                            'query' => $this->builder->query
                        ]
                    ]
                ],
                [
                    'match' => [
                        'text' => [
                            'query' => $this->builder->query,
                            'operator' => 'AND'
                        ]
                    ]
                ],
                [
                    'match' => [
                        'text' => [
                            'query' => $this->builder->query,
                            'fuzziness' => 'AUTO',
                            'lenient' => true,
                            'operator' => 'AND'
                        ]
                    ]
                ]
            ],
            'minimum_should_match' => 1
        ];
    }
}
