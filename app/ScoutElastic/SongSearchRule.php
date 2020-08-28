<?php

namespace App\ScoutElastic;

use ScoutElastic\SearchRule;

class SongSearchRule extends SearchRule
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
                        'title' => [
                            'query' => $this->builder->query,
                            'fuzziness' => 'AUTO',
                            'lenient' => true,
                            'operator' => 'AND'
                        ]
                    ]
                ],
                [
                    'wildcard' => [
                        'title' => [
                            'value' => $this->builder->query
                        ]
                    ]
                ]
            ],
            'minimum_should_match' => 1
        ];
    }
}
