<?php

declare(strict_types=1);

use ElasticAdapter\Indices\Mapping;
use ElasticMigrations\Facades\Index;
use ElasticMigrations\MigrationInterface;

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
                    'sort' => [
                        'type' => 'keyword',
                    ],
                ],
            ]);
            $mapping->long('series_id');
            $mapping->text('slug', [
                'fields' => [
                    'sort' => [
                        'type' => 'keyword',
                    ],
                ],
            ]);
            $mapping->date('updated_at');
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
