<?php

namespace App\ScoutElastic;

use ScoutElastic\SearchRule;

class ThemeSearchRule extends SearchRule
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
                        'slug' => [
                            'query' => $this->builder->query
                        ]
                    ]
                ],
                [
                    'match' => [
                        'slug' => [
                            'query' => $this->builder->query,
                            'operator' => 'AND'
                        ]
                    ]
                ],
                [
                    'match' => [
                        'slug' => [
                            'query' => $this->builder->query,
                            'fuzziness' => 'AUTO',
                            'lenient' => true,
                            'operator' => 'AND'
                        ]
                    ]
                ],
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
                ],
                [
                    'nested' => [
                        'path' => 'anime',
                        'query' => [
                            'bool' => [
                                'should' => [
                                    [
                                        'match_phrase' => [
                                            'anime.name' => [
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
                        'path' => 'anime',
                        'query' => [
                            'bool' => [
                                'should' => [
                                    [
                                        'match' => [
                                            'anime.name' => [
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
                        'path' => 'anime',
                        'query' => [
                            'bool' => [
                                'should' => [
                                    [
                                        'match' => [
                                            'anime.name' => [
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
                ],
                [
                    'nested' => [
                        'path' => 'anime',
                        'query' => [
                            'nested' => [
                                'path' => 'anime.synonyms',
                                'query' => [
                                    'bool' => [
                                        'should' => [
                                            [
                                                'match_phrase' => [
                                                    'anime.synonyms.text' => [
                                                        'query' => $this->builder->query
                                                    ]
                                                ]
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
                        'path' => 'anime',
                        'query' => [
                            'nested' => [
                                'path' => 'anime.synonyms',
                                'query' => [
                                    'bool' => [
                                        'should' => [
                                            [
                                                'match' => [
                                                    'anime.synonyms.text' => [
                                                        'query' => $this->builder->query,
                                                        'operator' => 'AND'
                                                    ]
                                                ]
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
                        'path' => 'anime',
                        'query' => [
                            'nested' => [
                                'path' => 'anime.synonyms',
                                'query' => [
                                    'bool' => [
                                        'should' => [
                                            [
                                                'match' => [
                                                    'anime.synonyms.text' => [
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
                    ]
                ],
                [
                    'nested' => [
                        'path' => 'song',
                        'query' => [
                            'bool' => [
                                'should' => [
                                    [
                                        'match_phrase' => [
                                            'song.title' => [
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
                        'path' => 'song',
                        'query' => [
                            'bool' => [
                                'should' => [
                                    [
                                        'match' => [
                                            'song.title' => [
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
                        'path' => 'song',
                        'query' => [
                            'bool' => [
                                'should' => [
                                    [
                                        'match' => [
                                            'song.title' => [
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
