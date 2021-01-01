<?php
declare(strict_types=1);

use ElasticAdapter\Indices\Mapping;
use ElasticAdapter\Indices\Settings;
use ElasticMigrations\Facades\Index;
use ElasticMigrations\MigrationInterface;

final class CreateEntryIndex implements MigrationInterface
{
    /**
     * Run the migration.
     */
    public function up(): void
    {
        Index::create('entry', function (Mapping $mapping, Settings $settings) {
            $mapping->text('anime_slug');
            $mapping->date('created_at');
            $mapping->long('entry_id');
            $mapping->text('episodes');
            $mapping->text('notes');
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
                        'type' => 'text',
                    ],
                    'sequence' => [
                        'type' => 'long',
                    ],
                    'slug' => [
                        'type' => 'text',
                        'copy_to' => [
                            'version_slug',
                            'anime_slug',
                            'synonym_slug',
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
                    'version_slug',
                    'anime_slug',
                    'synonym_slug',
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
        Index::dropIfExists('entry');
    }
}
