<?php

namespace App\ScoutElastic;

use ScoutElastic\SearchRule;

class ArtistSearchRule extends SearchRule
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
                                                'match_phrase' => [
                                                    'songs.pivot.as' => [
                                                        'query' => $this->builder->query,
                                                    ],
                                                ],
                                            ],
                                        ],
                                    ],
                                ],
                            ],
                        ],
                    ],
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
                                                        'operator' => 'AND',
                                                    ],
                                                ],
                                            ],
                                        ],
                                    ],
                                ],
                            ],
                        ],
                    ],
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
                                                        'operator' => 'AND',
                                                    ],
                                                ],
                                            ],
                                        ],
                                    ],
                                ],
                            ],
                        ],
                    ],
                ],
            ],
            'minimum_should_match' => 1,
        ];
    }
}
