<?php

declare(strict_types=1);

use Elastic\Adapter\Indices\Mapping;
use Elastic\Migrations\Facades\Index;
use Elastic\Migrations\MigrationInterface;

final class CreateArtistIndex implements MigrationInterface
{
    /**
     * Run the migration.
     */
    public function up(): void
    {
        Index::createIfNotExists('artists', function (Mapping $mapping) {
            $mapping->long('artist_id');
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
            $mapping->text('information', [
                'fields' => [
                    'keyword' => [
                        'type' => 'keyword',
                    ],
                ],
            ]);
            $mapping->nested('performances', [
                'properties' => [
                    'performance_id' => ['type' => 'long'],
                    'created_at' => ['type' => 'date'],
                    'updated_at' => ['type' => 'date'],
                    'alias' => ['type' => 'text'],
                    'as' => ['type' => 'text'],
                    'membership_alias' => ['type' => 'text'],
                    'membership_as' => ['type' => 'text'],
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
        Index::dropIfExists('artists');
    }
}
