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
                        'version' => [
                            'query' => $this->builder->query
                        ]
                    ]
                ],
                [
                    'match' => [
                        'version' => [
                            'query' => $this->builder->query,
                            'operator' => 'AND'
                        ]
                    ]
                ],
                [
                    'match' => [
                        'version' => [
                            'query' => $this->builder->query,
                            'fuzziness' => 'AUTO',
                            'lenient' => true,
                            'operator' => 'AND'
                        ]
                    ]
                ],
                [
                    'match_phrase' => [
                        'version_slug' => [
                            'query' => $this->builder->query
                        ]
                    ]
                ],
                [
                    'match' => [
                        'version_slug' => [
                            'query' => $this->builder->query,
                            'operator' => 'AND'
                        ]
                    ]
                ],
                [
                    'match' => [
                        'version_slug' => [
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
                        'path' => 'theme',
                        'query' => [
                            'nested' => [
                                'path' => 'theme.anime',
                                'query' => [
                                    'bool' => [
                                        'should' => [
                                            [
                                                'match_phrase' => [
                                                    'theme.anime.name' => [
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
                        'path' => 'theme',
                        'query' => [
                            'nested' => [
                                'path' => 'theme.anime',
                                'query' => [
                                    'bool' => [
                                        'should' => [
                                            [
                                                'match' => [
                                                    'theme.anime.name' => [
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
                        'path' => 'theme',
                        'query' => [
                            'nested' => [
                                'path' => 'theme.anime',
                                'query' => [
                                    'bool' => [
                                        'should' => [
                                            [
                                                'match' => [
                                                    'theme.anime.name' => [
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
                        'path' => 'theme',
                        'query' => [
                            'nested' => [
                                'path' => 'theme.anime',
                                'query' => [
                                    'nested' => [
                                        'path' => 'theme.anime.synonyms',
                                        'query' => [
                                            'bool' => [
                                                'should' => [
                                                    [
                                                        'match_phrase' => [
                                                            'theme.anime.synonyms.text' => [
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
                        ]
                    ]
                ],
                [
                    'nested' => [
                        'path' => 'theme',
                        'query' => [
                            'nested' => [
                                'path' => 'theme.anime',
                                'query' => [
                                    'nested' => [
                                        'path' => 'theme.anime.synonyms',
                                        'query' => [
                                            'bool' => [
                                                'should' => [
                                                    [
                                                        'match' => [
                                                            'theme.anime.synonyms.text' => [
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
                        ]
                    ]
                ],
                [
                    'nested' => [
                        'path' => 'theme',
                        'query' => [
                            'nested' => [
                                'path' => 'theme.anime',
                                'query' => [
                                    'nested' => [
                                        'path' => 'theme.anime.synonyms',
                                        'query' => [
                                            'bool' => [
                                                'should' => [
                                                    [
                                                        'match' => [
                                                            'theme.anime.synonyms.text' => [
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
                        ]
                    ]
                ],
                [
                    'nested' => [
                        'path' => 'theme',
                        'query' => [
                            'nested' => [
                                'path' => 'theme.song',
                                'query' => [
                                    'bool' => [
                                        'should' => [
                                            [
                                                'match_phrase' => [
                                                    'theme.song.title' => [
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
                        'path' => 'theme',
                        'query' => [
                            'nested' => [
                                'path' => 'theme.song',
                                'query' => [
                                    'bool' => [
                                        'should' => [
                                            [
                                                'match' => [
                                                    'theme.song.title' => [
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
                        'path' => 'theme',
                        'query' => [
                            'nested' => [
                                'path' => 'theme.song',
                                'query' => [
                                    'bool' => [
                                        'should' => [
                                            [
                                                'match' => [
                                                    'theme.song.title' => [
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
            ],
            'minimum_should_match' => 1
        ];
    }
}
