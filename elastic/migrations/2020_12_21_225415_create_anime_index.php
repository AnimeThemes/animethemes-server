<?php

declare(strict_types=1);

use ElasticAdapter\Indices\Mapping;
use ElasticMigrations\Facades\Index;
use ElasticMigrations\MigrationInterface;

/**
 * Class CreateAnimeIndex.
 */
final class CreateAnimeIndex implements MigrationInterface
{
    /**
     * Run the migration.
     */
    public function up(): void
    {
        Index::createIfNotExists('anime', function (Mapping $mapping) {
            $mapping->long('anime_id');
            $mapping->date('created_at');
            $mapping->text('name', [
                'fields' => [
                    'sort' => [
                        'type' => 'keyword',
                    ],
                ],
            ]);
            $mapping->long('season');
            $mapping->text('slug', [
                'fields' => [
                    'sort' => [
                        'type' => 'keyword',
                    ],
                ],
            ]);
            $mapping->nested('synonyms', [
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
                    ],
                    'updated_at' => [
                        'type' => 'date',
                    ],
                ],
            ]);
            $mapping->text('synopsis', [
                'fields' => [
                    'sort' => [
                        'type' => 'keyword',
                    ],
                ],
            ]);
            $mapping->date('updated_at');
            $mapping->long('year');
        });
    }

    /**
     * Reverse the migration.
     */
    public function down(): void
    {
        Index::dropIfExists('anime');
    }
}
