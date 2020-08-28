<?php

namespace App\ScoutElastic;

use ScoutElastic\SearchRule;

class ArtistSearchRule extends SearchRule
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
                        'path' => 'songs',
                        'query' => [
                            'nested' => [
                                'path' => 'songs.pivot',
                                'query' => [
                                    'bool' => [
                                        'should' => [
                                            [
                                                'match' => [
                                                    'songs.pivot.as' => [
                                                        'query' => $this->builder->query,
                                                        'fuzziness' => 'AUTO',
                                                        'lenient' => true,
                                                        'operator' => 'AND'
                                                    ]
                                                ]
                                            ],
                                            [
                                                'wildcard' => [
                                                    'songs.pivot.as' => [
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
                    ]
                ]
            ],
            'minimum_should_match' => 1
        ];
    }
}
