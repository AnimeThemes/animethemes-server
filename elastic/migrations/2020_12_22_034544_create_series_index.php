<?php

declare(strict_types=1);

use Elastic\Adapter\Indices\Mapping;
use Elastic\Migrations\Facades\Index;
use Elastic\Migrations\MigrationInterface;

/**
 * Class CreateSeriesIndex.
 */
final class CreateSeriesIndex implements MigrationInterface
{
    /**
     * Run the migration.
     */
    public function up(): void
    {
        Index::createIfNotExists('series', function (Mapping $mapping) {
            $mapping->date('created_at');
            $mapping->text('name', [
                'fields' => [
                    'keyword' => [
                        'type' => 'keyword',
                    ],
                ],
            ]);
            $mapping->long('series_id');
            $mapping->text('slug', [
                'fields' => [
                    'keyword' => [
                        'type' => 'keyword',
                    ],
                ],
            ]);
            $mapping->date('updated_at');
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
            ]);
            $mapping->text('anime_slug');
            $mapping->text('synonym_slug');
        });
    }

    /**
     * Reverse the migration.
     */
    public function down(): void
    {
        Index::dropIfExists('series');
    }
}
