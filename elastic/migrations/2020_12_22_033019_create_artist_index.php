<?php

declare(strict_types=1);

use Elastic\Adapter\Indices\Mapping;
use Elastic\Migrations\Facades\Index;
use Elastic\Migrations\MigrationInterface;

/**
 * Class CreateArtistIndex.
 */
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
            $mapping->nested('songs', [
                'properties' => [
                    'created_at' => [
                        'type' => 'date',
                    ],
                    'pivot' => [
                        'type' => 'nested',
                        'properties' => [
                            'artist_id' => [
                                'type' => 'long',
                            ],
                            'as' => [
                                'type' => 'text',
                            ],
                            'song_id' => [
                                'type' => 'long',
                            ],
                        ],
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
