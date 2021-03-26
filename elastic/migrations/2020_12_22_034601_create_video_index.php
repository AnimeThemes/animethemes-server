<?php

declare(strict_types=1);

use ElasticAdapter\Indices\Mapping;
use ElasticAdapter\Indices\Settings;
use ElasticMigrations\Facades\Index;
use ElasticMigrations\MigrationInterface;

final class CreateVideoIndex implements MigrationInterface
{
    /**
     * Run the migration.
     */
    public function up(): void
    {
        Index::create('video', function (Mapping $mapping, Settings $settings) {
            $mapping->text('anime_slug');
            $mapping->text('basename');
            $mapping->date('created_at');
            $mapping->nested('entries', [
                'properties' => [
                    'created_at' => [
                        'type' => 'date',
                    ],
                    'entry_id' => [
                        'type' => 'long',
                    ],
                    'episodes' => [
                        'type' => 'text',
                    ],
                    'notes' => [
                        'type' => 'text',
                    ],
                    'nsfw' => [
                        'type' => 'boolean',
                    ],
                    'spoiler' => [
                        'type' => 'boolean',
                    ],
                    'theme' => [
                        'type' => 'nested',
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
                                    'anime_slug',
                                    'synonym_slug',
                                    'tags_slug',
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
                    ],
                    'theme_id' => [
                        'type' => 'long',
                    ],
                    'updated_at' => [
                        'type' => 'date',
                    ],
                    'version' => [
                        'type' => 'text',
                        'copy_to' => [
                            'anime_slug',
                            'synonym_slug',
                            'tags_slug',
                            'version_slug',
                        ],
                    ],
                ],
            ]);
            $mapping->text('filename');
            $mapping->boolean('lyrics');
            $mapping->boolean('nc');
            $mapping->long('overlap');
            $mapping->text('path');
            $mapping->long('resolution');
            $mapping->long('size');
            $mapping->string('mimetype');
            $mapping->long('source');
            $mapping->boolean('subbed');
            $mapping->text('synonym_slug');
            $mapping->text('tags', [
                'copy_to' => [
                    'anime_slug',
                    'synonym_slug',
                    'tags_slug',
                ],
            ]);
            $mapping->text('tags_slug');
            $mapping->boolean('uncen');
            $mapping->date('updated_at');
            $mapping->text('version_slug');
            $mapping->long('video_id');
        });
    }

    /**
     * Reverse the migration.
     */
    public function down(): void
    {
        Index::dropIfExists('video');
    }
}
