<?php

declare(strict_types=1);

namespace App\Console\Commands\Storage\Admin;

use App\Actions\Storage\Admin\Dump\DumpAction;
use App\Actions\Storage\Admin\Dump\DumpDocumentAction;

/**
 * Class DocumentDumpCommand.
 */
class DocumentDumpCommand extends DumpCommand
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'db:dump-document
        {--comments : Write additional information in the MySQL dump such as program version, server version and host}
        {--data-only : Dump only the data without the schema in PostgreSQL dump}
        {--default-character-set=utf8 : Specify default character set in MySQL dump}
        {--extended-insert : Use multiple-row insert syntax in MySQL dump}
        {--inserts : Dump data as INSERT commands rather than COPY in PostgreSQL dump}
        {--lock-tables : Lock all tables before dumping them to MySQL dump}
        {--no-create-info : Turn off CREATE TABLE statements in MySQL dump}
        {--quick : Retrieve rows for a table from the server one row at a time in MySQL dump}
        {--set-gtid-purged=AUTO : Add SET GTID_PURGED to output in MySQL dump}
        {--single-transaction=true : Issue a BEGIN SQL statement before dumping data from server for MySQL dump}
        {--skip-column-statistics : Turn off ANALYZE table statements in the MySQL dump}
        {--skip-comments : Do not write additional information in the MySQL dump}
        {--skip-extended-insert : Turn off extended-insert in MySQL dump}
        {--skip-lock-tables : Turn off locking tables before dumping to MySQL dump}
        {--skip-quick : Do not retrieve rows for a table from the server one row at a time in MySQL dump}
        ';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Produces sanitized database dump, targeting document-related tables for seeding purposes';

    /**
     * Get the underlying action.
     *
     * @return DumpAction
     */
    protected function action(): DumpAction
    {
        return new DumpDocumentAction($this->options());
    }
}
