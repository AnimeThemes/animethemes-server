<?php

namespace App\ScoutElastic;

use ScoutElastic\SearchRule;

class AnimeSearchRule extends SearchRule
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
                        'name' => [
                            'query' => $this->builder->query
                        ]
                    ]
                ],
                [
                    'match' => [
                        'name' => [
                            'query' => $this->builder->query,
                            'operator' => 'AND'
                        ]
                    ]
                ],
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
                    'nested' => [
                        'path' => 'synonyms',
                        'query' => [
                            'bool' => [
                                'should' => [
                                    [
                                        'match_phrase' => [
                                            'synonyms.text' => [
                                                'query' => $this->builder->query
                                            ]
                                        ]
                                    ]
                                ]
                            ]
                        ]
                    ]
                ],
                [
                    'nested' => [
                        'path' => 'synonyms',
                        'query' => [
                            'bool' => [
                                'should' => [
                                    [
                                        'match' => [
                                            'synonyms.text' => [
                                                'query' => $this->builder->query,
                                                'operator' => 'AND'
                                            ]
                                        ]
                                    ]
                                ]
                            ]
                        ]
                    ]
                ],
                [
                    'nested' => [
                        'path' => 'synonyms',
                        'query' => [
                            'bool' => [
                                'should' => [
                                    [
                                        'match' => [
                                            'synonyms.text' => [
                                                'query' => $this->builder->query,
                                                'fuzziness' => 'AUTO',
                                                'lenient' => true,
                                                'operator' => 'AND'
                                            ]
                                        ]
                                    ]
                                ]
                            ]
                        ]
                    ]
                ]
            ],
            'minimum_should_match' => 1
        ];
    }
}
