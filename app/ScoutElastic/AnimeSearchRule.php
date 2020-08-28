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
                                    ],
                                    [
                                        'wildcard' => [
                                            'synonyms.text' => [
                                                'value' => $this->builder->query
                                            ]
                                        ]
                                    ]
                                ],
                                'minimum_should_match' => 1
                            ]
                        ]
                    ]
                ]
            ],
            'minimum_should_match' => 1
        ];
    }
}
