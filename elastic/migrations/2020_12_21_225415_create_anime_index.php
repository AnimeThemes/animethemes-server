<?php

declare(strict_types=1);

use ElasticAdapter\Indices\Mapping;
use ElasticAdapter\Indices\Settings;
use ElasticMigrations\Facades\Index;
use ElasticMigrations\MigrationInterface;

final class CreateAnimeIndex implements MigrationInterface
{
    /**
     * Run the migration.
     */
    public function up(): void
    {
        Index::create('anime', function (Mapping $mapping, Settings $settings) {
            $mapping->long('anime_id');
            $mapping->date('created_at');
            $mapping->text('name');
            $mapping->long('season');
            $mapping->text('slug');
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
            $mapping->text('synopsis');
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
