<?php

namespace App\ScoutElastic;

use ScoutElastic\SearchRule;

class VideoSearchRule extends SearchRule
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
                        'filename' => [
                            'query' => $this->builder->query
                        ]
                    ]
                ],
                [
                    'match' => [
                        'filename' => [
                            'query' => $this->builder->query,
                            'operator' => 'AND'
                        ]
                    ]
                ],
                [
                    'match' => [
                        'filename' => [
                            'query' => $this->builder->query,
                            'fuzziness' => 'AUTO',
                            'lenient' => true,
                            'operator' => 'AND'
                        ]
                    ]
                ],
                [
                    'match_phrase' => [
                        'tags' => [
                            'query' => $this->builder->query
                        ]
                    ]
                ],
                [
                    'match' => [
                        'tags' => [
                            'query' => $this->builder->query,
                            'operator' => 'AND'
                        ]
                    ]
                ],
                [
                    'match' => [
                        'tags' => [
                            'query' => $this->builder->query,
                            'fuzziness' => 'AUTO',
                            'lenient' => true,
                            'operator' => 'AND'
                        ]
                    ]
                ],
                [
                    'match_phrase' => [
                        'tags_slug' => [
                            'query' => $this->builder->query
                        ]
                    ]
                ],
                [
                    'match' => [
                        'tags_slug' => [
                            'query' => $this->builder->query,
                            'operator' => 'AND'
                        ]
                    ]
                ],
                [
                    'match' => [
                        'tags_slug' => [
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
                        'path' => 'entries',
                        'query' => [
                            'nested' => [
                                'path' => 'entries.theme',
                                'query' => [
                                    'nested' => [
                                        'path' => 'entries.theme.anime',
                                        'query' => [
                                            'bool' => [
                                                'should' => [
                                                    [
                                                        'match_phrase' => [
                                                            'entries.theme.anime.name' => [
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
                        'path' => 'entries',
                        'query' => [
                            'nested' => [
                                'path' => 'entries.theme',
                                'query' => [
                                    'nested' => [
                                        'path' => 'entries.theme.anime',
                                        'query' => [
                                            'bool' => [
                                                'should' => [
                                                    [
                                                        'match' => [
                                                            'entries.theme.anime.name' => [
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
                        'path' => 'entries',
                        'query' => [
                            'nested' => [
                                'path' => 'entries.theme',
                                'query' => [
                                    'nested' => [
                                        'path' => 'entries.theme.anime',
                                        'query' => [
                                            'bool' => [
                                                'should' => [
                                                    [
                                                        'match' => [
                                                            'entries.theme.anime.name' => [
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
                        'path' => 'entries',
                        'query' => [
                            'nested' => [
                                'path' => 'entries.theme',
                                'query' => [
                                    'nested' => [
                                        'path' => 'entries.theme.anime',
                                        'query' => [
                                            'nested' => [
                                                'path' => 'entries.theme.anime.synonyms',
                                                'query' => [
                                                    'bool' => [
                                                        'should' => [
                                                            [
                                                                'match_phrase' => [
                                                                    'entries.theme.anime.synonyms.text' => [
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
                        ]
                    ]
                ],
                [
                    'nested' => [
                        'path' => 'entries',
                        'query' => [
                            'nested' => [
                                'path' => 'entries.theme',
                                'query' => [
                                    'nested' => [
                                        'path' => 'entries.theme.anime',
                                        'query' => [
                                            'nested' => [
                                                'path' => 'entries.theme.anime.synonyms',
                                                'query' => [
                                                    'bool' => [
                                                        'should' => [
                                                            [
                                                                'match' => [
                                                                    'entries.theme.anime.synonyms.text' => [
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
                        ]
                    ]
                ],
                [
                    'nested' => [
                        'path' => 'entries',
                        'query' => [
                            'nested' => [
                                'path' => 'entries.theme',
                                'query' => [
                                    'nested' => [
                                        'path' => 'entries.theme.anime',
                                        'query' => [
                                            'nested' => [
                                                'path' => 'entries.theme.anime.synonyms',
                                                'query' => [
                                                    'bool' => [
                                                        'should' => [
                                                            [
                                                                'match' => [
                                                                    'entries.theme.anime.synonyms.text' => [
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
                        ]
                    ]
                ],
                [
                    'nested' => [
                        'path' => 'entries',
                        'query' => [
                            'nested' => [
                                'path' => 'entries.theme',
                                'query' => [
                                    'nested' => [
                                        'path' => 'entries.theme.song',
                                        'query' => [
                                            'bool' => [
                                                'should' => [
                                                    [
                                                        'match_phrase' => [
                                                            'entries.theme.song.title' => [
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
                        'path' => 'entries',
                        'query' => [
                            'nested' => [
                                'path' => 'entries.theme',
                                'query' => [
                                    'nested' => [
                                        'path' => 'entries.theme.song',
                                        'query' => [
                                            'bool' => [
                                                'should' => [
                                                    [
                                                        'match' => [
                                                            'entries.theme.song.title' => [
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
                        'path' => 'entries',
                        'query' => [
                            'nested' => [
                                'path' => 'entries.theme',
                                'query' => [
                                    'nested' => [
                                        'path' => 'entries.theme.song',
                                        'query' => [
                                            'bool' => [
                                                'should' => [
                                                    [
                                                        'match' => [
                                                            'entries.theme.song.title' => [
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
            ],
            'minimum_should_match' => 1
        ];
    }
}
