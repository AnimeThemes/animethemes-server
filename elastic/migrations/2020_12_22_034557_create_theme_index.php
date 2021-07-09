<?php

declare(strict_types=1);

use ElasticAdapter\Indices\Mapping;
use ElasticMigrations\Facades\Index;
use ElasticMigrations\MigrationInterface;

/**
 * Class CreateThemeIndex.
 */
final class CreateThemeIndex implements MigrationInterface
{
    /**
     * Run the migration.
     */
    public function up(): void
    {
        Index::create('theme', function (Mapping $mapping) {
            $mapping->nested('anime', [
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
            ]);
            $mapping->long('anime_id');
            $mapping->text('anime_slug');
            $mapping->date('created_at');
            $mapping->text('group');
            $mapping->long('sequence');
            $mapping->text('slug', [
                'copy_to' => [
                    'anime_slug',
                    'synonym_slug',
                ],
            ]);
            $mapping->nested('song', [
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
            ]);
            $mapping->long('song_id');
            $mapping->text('synonym_slug');
            $mapping->long('theme_id');
            $mapping->long('type');
            $mapping->date('updated_at');
        });
    }

    /**
     * Reverse the migration.
     */
    public function down(): void
    {
        Index::dropIfExists('theme');
    }
}