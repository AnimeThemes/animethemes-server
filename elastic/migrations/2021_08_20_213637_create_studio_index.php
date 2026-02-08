<?php

declare(strict_types=1);

use App\Concerns\Elastic\ConfiguresTextAnalyzers;
use Elastic\Adapter\Indices\Mapping;
use Elastic\Adapter\Indices\Settings;
use Elastic\Migrations\Facades\Index;
use Elastic\Migrations\MigrationInterface;

final class CreateStudioIndex implements MigrationInterface
{
    use ConfiguresTextAnalyzers;

    /**
     * Run the migration.
     */
    public function up(): void
    {
        Index::createIfNotExists('studios', function (Mapping $mapping, Settings $settings) {
            $this->configureTextAnalyzers($settings);

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
