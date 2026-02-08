<?php

declare(strict_types=1);

namespace App\Concerns\Elastic;

use Elastic\Adapter\Indices\Settings;

trait ConfiguresTextAnalyzers
{
    protected function configureTextAnalyzers(Settings $settings): void
    {
        $settings->analysis([
            'analyzer' => [
                'name_search' => [
                    'type' => 'custom',
                    'tokenizer' => 'standard',
                    'filter' => [
                        'lowercase',
                        'word_delimiter_graph',
                    ],
                ],
            ],
        ]);
    }
}
