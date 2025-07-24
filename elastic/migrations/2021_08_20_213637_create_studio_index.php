<?php

declare(strict_types=1);

use Elastic\Adapter\Indices\Mapping;
use Elastic\Migrations\Facades\Index;
use Elastic\Migrations\MigrationInterface;

final class CreateStudioIndex implements MigrationInterface
{
    /**
     * Run the migration.
     */
    public function up(): void
    {
        Index::createIfNotExists('studios', function (Mapping $mapping) {
            $mapping->date('created_at');
            $mapping->text('name', [
                'fields' => [
                    'keyword' => [
                        'type' => 'keyword',
                    ],
                ],
            ]);
            $mapping->text('slug', [
                'fields' => [
                    'keyword' => [
                        'type' => 'keyword',
                    ],
                ],
            ]);
            $mapping->long('studio_id');
            $mapping->date('updated_at');
        });
    }

    /**
     * Reverse the migration.
     */
    public function down(): void
    {
        Index::dropIfExists('studios');
    }
}
