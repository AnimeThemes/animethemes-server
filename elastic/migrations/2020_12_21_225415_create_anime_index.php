<?php

declare(strict_types=1);

use Elastic\Adapter\Indices\Mapping;
use Elastic\Migrations\Facades\Index;
use Elastic\Migrations\MigrationInterface;

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
                    'keyword' => [
                        'type' => 'keyword',
                    ],
                ],
            ]);
            $mapping->long('season');
            $mapping->long('media_format');
            $mapping->text('slug', [
                'fields' => [
                    'keyword' => [
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
                    'type' => [
                        'type' => 'long',
                    ],
                    'updated_at' => [
                        'type' => 'date',
                    ],
                ],
            ]);
            $mapping->text('synopsis', [
                'fields' => [
                    'keyword' => [
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
