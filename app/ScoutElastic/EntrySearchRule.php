<?php

namespace App\ScoutElastic;

use ScoutElastic\SearchRule;

class EntrySearchRule extends SearchRule
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
                    'match_phrase' => [
                        'anime_slug' => [
                            'query' => $this->builder->query
                        ]
                    ]
                ],
                [
                    'match' => [
                        'anime_slug' => [
                            'query' => $this->builder->query,
                            'operator' => 'AND'
                        ]
                    ]
                ],
                [
                    'match' => [
                        'anime_slug' => [
                            'query' => $this->builder->query,
                            'fuzziness' => 'AUTO',
                            'lenient' => true,
                            'operator' => 'AND'
                        ]
                    ]
                ],
                [
                    'match_phrase' => [
                        'synonym_slug' => [
                            'query' => $this->builder->query
                        ]
                    ]
                ],
                [
                    'match' => [
                        'synonym_slug' => [
                            'query' => $this->builder->query,
                            'operator' => 'AND'
                        ]
                    ]
                ],
                [
                    'match' => [
                        'synonym_slug' => [
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
