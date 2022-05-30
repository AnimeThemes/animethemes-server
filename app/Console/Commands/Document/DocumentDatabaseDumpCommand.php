<?php

declare(strict_types=1);

namespace App\Console\Commands\Document;

use App\Console\Commands\DatabaseDumpCommand;
use App\Models\Document\Page;

/**
 * Class DocumentDatabaseDumpCommand.
 */
class DocumentDatabaseDumpCommand extends DatabaseDumpCommand
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'db:dump-document {--C|create : Whether the dumper should include create table statements}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Produces sanitized database dump, targeting document-related tables for seeding purposes';

    /**
     * The list of tables to include in the dump.
     *
     * @return array
     */
    protected function allowedTables(): array
    {
        return [
            Page::TABLE,
        ];
    }

    /**
     * The directory that the file should be dumped to.
     *
     * @return string
     */
    protected function getDumpFilePath(): string
    {
        return 'document';
    }
}
