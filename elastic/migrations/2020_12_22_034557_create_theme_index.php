<?php

declare(strict_types=1);

use App\Concerns\Elastic\ConfiguresTextAnalyzers;
use Elastic\Adapter\Indices\Mapping;
use Elastic\Adapter\Indices\Settings;
use Elastic\Migrations\Facades\Index;
use Elastic\Migrations\MigrationInterface;

final class CreateThemeIndex implements MigrationInterface
{
    use ConfiguresTextAnalyzers;

    /**
     * Run the migration.
     */
    public function up(): void
    {
        Index::createIfNotExists('anime_themes', function (Mapping $mapping, Settings $settings) {
            $this->configureTextAnalyzers($settings);

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
                        'analyzer' => 'name_search',
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
                    'synonyms' => [
                        'type' => 'text',
                        'analyzer' => 'name_search',
                        'copy_to' => ['synonym_slug'],
                        'fields' => [
                            'keyword' => [
                                'type' => 'keyword',
                            ],
                        ],
                    ],
                ],
            ]);
            $mapping->long('anime_id');
            $mapping->text('anime_slug');
            $mapping->date('created_at');
            $mapping->long('group_id');
            $mapping->long('sequence');
            $mapping->text('slug', [
                'copy_to' => [
                    'anime_slug',
                    'synonym_slug',
                ],
                'fields' => [
                    'keyword' => [
                        'type' => 'keyword',
                    ],
                ],
            ]);
            $mapping->nested('song', [
                'properties' => [
                    'created_at' => [
                        'type' => 'date',
                    ],
                    'song_id' => [
                        'type' => 'long',
                    ],
                    'title' => [
                        'type' => 'text',
                        'analyzer' => 'name_search',
                    ],
                    'title_keyword' => [
                        'type' => 'keyword',
                    ],
                    'title_native' => [
                        'type' => 'text',
                        'analyzer' => 'name_search',
                    ],
                    'title_native_keyword' => [
                        'type' => 'keyword',
                    ],
                    'updated_at' => [
                        'type' => 'date',
                    ],
                ],
            ]);
            $mapping->long('song_id');
            $mapping->text('synonym_slug');
            $mapping->long('theme_id');
            $mapping->long('type');
            $mapping->date('updated_at');
        });
    }

    /**
     * Reverse the migration.
     */
    public function down(): void
    {
        Index::dropIfExists('anime_themes');
    }
}
