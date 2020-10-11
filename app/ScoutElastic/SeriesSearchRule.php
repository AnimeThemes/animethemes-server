<?php

namespace App\ScoutElastic;

use ScoutElastic\SearchRule;

class SeriesSearchRule extends SearchRule
{
    /**
     * {@inheritdoc}
     */
    public function buildQueryPayload()
    {
        return [
            'should' => [
                [
                    'match_phrase' => [
                        'name' => [
                            'query' => $this->builder->query,
                        ],
                    ],
                ],
                [
                    'match' => [
                        'name' => [
                            'query' => $this->builder->query,
                            'operator' => 'AND',
                        ],
                    ],
                ],
                [
                    'match' => [
                        'name' => [
                            'query' => $this->builder->query,
                            'fuzziness' => 'AUTO',
                            'lenient' => true,
                            'operator' => 'AND',
                        ],
                    ],
                ],
            ],
            'minimum_should_match' => 1,
        ];
    }
}
