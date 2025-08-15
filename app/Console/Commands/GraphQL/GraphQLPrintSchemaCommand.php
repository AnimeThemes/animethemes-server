<?php

declare(strict_types=1);

namespace App\Console\Commands\GraphQL;

use App\Console\Commands\BaseCommand;
use GraphQL\Utils\SchemaPrinter;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Support\Facades\Validator as ValidatorFacade;
use Rebing\GraphQL\Support\Facades\GraphQL;

class GraphQLPrintSchemaCommand extends BaseCommand
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'graphql:print-schema {schema=default}';

    /**
     * The console command description.
     */
    protected $description = 'Print the GraphQL schema into SDL';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $schemaName = $this->argument('schema');

        $schema = GraphQL::schema($schemaName);

        $sdl = SchemaPrinter::doPrint($schema);

        $this->line($sdl);

        return 0;
    }

    /**
     * Get the validator for options.
     */
    protected function validator(): Validator
    {
        return ValidatorFacade::make($this->options(), []);
    }
}
