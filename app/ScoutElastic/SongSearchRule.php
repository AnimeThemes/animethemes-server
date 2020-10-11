<?php

namespace App\ScoutElastic;

use ScoutElastic\SearchRule;

class SongSearchRule extends SearchRule
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
                        'title' => [
                            'query' => $this->builder->query,
                        ],
                    ],
                ],
                [
                    'match' => [
                        'title' => [
                            'query' => $this->builder->query,
                            'operator' => 'AND',
                        ],
                    ],
                ],
                [
                    'match' => [
                        'title' => [
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
