<?php

declare(strict_types=1);

use Elastic\Adapter\Indices\Mapping;
use Elastic\Migrations\Facades\Index;
use Elastic\Migrations\MigrationInterface;

/**
 * Class CreateSynonymIndex.
 */
final class CreateSynonymIndex implements MigrationInterface
{
    /**
     * Run the migration.
     */
    public function up(): void
    {
        Index::createIfNotExists('anime_synonyms', function (Mapping $mapping) {
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
                    ],
                    'media_format' => [
                        'type' => 'long',
                    ],
                    'season' => [
                        'type' => 'long',
                    ],
                    'slug' => [
                        'type' => 'text',
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
            $mapping->date('created_at');
            $mapping->long('synonym_id');
            $mapping->text('text', [
                'fields' => [
                    'keyword' => [
                        'type' => 'keyword',
                    ],
                ],
            ]);
            $mapping->long('type');
            $mapping->date('updated_at');
        });
    }

    /**
     * Reverse the migration.
     */
    public function down(): void
    {
        Index::dropIfExists('anime_synonyms');
    }
}
