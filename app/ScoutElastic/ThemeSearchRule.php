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
                    'wildcard' => [
                        'anime_slug' => [
                            'value' => $this->builder->query
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
                    'wildcard' => [
                        'synonym_slug' => [
                            'value' => $this->builder->query
                        ]
                    ]
                ]
            ],
            'minimum_should_match' => 1
        ];
    }
}
