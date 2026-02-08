<?php

declare(strict_types=1);

use App\Concerns\Elastic\ConfiguresTextAnalyzers;
use Elastic\Adapter\Indices\Mapping;
use Elastic\Adapter\Indices\Settings;
use Elastic\Migrations\Facades\Index;
use Elastic\Migrations\MigrationInterface;

final class CreateEntryIndex implements MigrationInterface
{
    use ConfiguresTextAnalyzers;

    /**
     * Run the migration.
     */
    public function up(): void
    {
        Index::createIfNotExists('anime_theme_entries', function (Mapping $mapping, Settings $settings) {
            $this->configureTextAnalyzers($settings);

            $mapping->text('anime_slug');
            $mapping->date('created_at');
            $mapping->long('entry_id');
            $mapping->text('episodes', [
                'fields' => [
                    'keyword' => [
                        'type' => 'keyword',
                    ],
                ],
            ]);
            $mapping->text('notes', [
                'fields' => [
                    'keyword' => [
                        'type' => 'keyword',
                    ],
                ],
            ]);
            $mapping->boolean('nsfw');
            $mapping->boolean('spoiler');
            $mapping->text('synonym_slug');
            $mapping->nested('theme', [
                'properties' => [
                    'anime' => [
                        'type' => 'nested',
                        'properties' => [
                            'anime_id' => [
                                'type' => 'long',
                            ],
                            'created_at' => [
                                'type' => 'date',
                            ],
                            'name' => [
                                'type' => 'text',
                                'copy_to' => ['anime_slug'],
                                'analyzer' => 'name_search',
                            ],
                            'season' => [
                                'type' => 'long',
                            ],
                            'slug' => [
                                'type' => 'text',
                            ],
                            'synonyms' => [
                                'type' => 'nested',
                                'properties' => [
                                    'anime_id' => [
                                        'type' => 'long',
                                    ],
                                    'created_at' => [
                                        'type' => 'date',
                                    ],
                                    'synonym_id' => [
                                        'type' => 'long',
                                    ],
                                    'text' => [
                                        'type' => 'text',
                                        'copy_to' => ['synonym_slug'],
                                        'analyzer' => 'name_search',
                                    ],
                                    'type' => [
                                        'type' => 'long',
                                    ],
                                    'updated_at' => [
                                        'type' => 'date',
                                    ],
                                ],
                            ],
                            'synopsis' => [
                                'type' => 'text',
                            ],
                            'updated_at' => [
                                'type' => 'date',
                            ],
                            'year' => [
                                'type' => 'long',
                            ],
                        ],
                    ],
                    'anime_id' => [
                        'type' => 'long',
                    ],
                    'created_at' => [
                        'type' => 'date',
                    ],
                    'group' => [
                        'type' => 'nested',
                        'properties' => [
                            'created_at' => [
                                'type' => 'date',
                            ],
                            'group_id' => [
                                'type' => 'long',
                            ],
                            'name' => [
                                'type' => 'text',
                            ],
                            'slug' => [
                                'type' => 'text',
                            ],
                            'updated_at' => [
                                'type' => 'date',
                            ],
                        ],
                    ],
                    'group_id' => [
                        'type' => 'long',
                    ],
                    'sequence' => [
                        'type' => 'long',
                    ],
                    'slug' => [
                        'type' => 'text',
                        'copy_to' => [
                            'anime_slug',
                            'synonym_slug',
                            'version_slug',
                        ],
                    ],
                    'song' => [
                        'type' => 'nested',
                        'properties' => [
                            'created_at' => [
                                'type' => 'date',
                            ],
                            'song_id' => [
                                'type' => 'long',
                            ],
                            'title' => [
                                'type' => 'text',
                                'analyzer' => 'name_search',
                            ],
                            'title_native' => [
                                'type' => 'text',
                                'analyzer' => 'name_search',
                            ],
                            'updated_at' => [
                                'type' => 'date',
                            ],
                        ],
                    ],
                    'song_id' => [
                        'type' => 'long',
                    ],
                    'theme_id' => [
                        'type' => 'long',
                    ],
                    'type' => [
                        'type' => 'long',
                    ],
                    'updated_at' => [
                        'type' => 'date',
                    ],
                ],
            ]);
            $mapping->long('theme_id');
            $mapping->date('updated_at');
            $mapping->text('version', [
                'copy_to' => [
                    'anime_slug',
                    'synonym_slug',
                    'version_slug',
                ],
                'fields' => [
                    'keyword' => [
                        'type' => 'keyword',
                    ],
                ],
            ]);
            $mapping->text('version_slug');
        });
    }

    /**
     * Reverse the migration.
     */
    public function down(): void
    {
        Index::dropIfExists('anime_theme_entries');
    }
}
