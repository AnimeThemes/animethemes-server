<?php

declare(strict_types=1);

use App\Concerns\Elastic\ConfiguresTextAnalyzers;
use Elastic\Adapter\Indices\Mapping;
use Elastic\Adapter\Indices\Settings;
use Elastic\Migrations\Facades\Index;
use Elastic\Migrations\MigrationInterface;

final class CreateArtistIndex implements MigrationInterface
{
    use ConfiguresTextAnalyzers;

    /**
     * Run the migration.
     */
    public function up(): void
    {
        Index::createIfNotExists('artists', function (Mapping $mapping, Settings $settings) {
            $this->configureTextAnalyzers($settings);

            $mapping->long('artist_id');
            $mapping->date('created_at');
            $mapping->text('name', [
                'analyzer' => 'name_search',
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
                    'performance_id' => [
                        'type' => 'long',
                    ],
                    'created_at' => [
                        'type' => 'date',
                    ],
                    'updated_at' => [
                        'type' => 'date',
                    ],
                    'alias' => [
                        'type' => 'text',
                        'analyzer' => 'name_search',
                    ],
                    'as' => [
                        'type' => 'text',
                        'analyzer' => 'name_search',
                    ],
                    'membership_alias' => [
                        'type' => 'text',
                        'analyzer' => 'name_search',
                    ],
                    'membership_as' => [
                        'type' => 'text',
                        'analyzer' => 'name_search',
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
