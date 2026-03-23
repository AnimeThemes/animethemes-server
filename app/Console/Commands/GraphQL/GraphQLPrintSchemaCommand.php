<?php

declare(strict_types=1);

namespace App\Console\Commands\GraphQL;

use App\Console\Commands\BaseCommand;
use GraphQL\Utils\SchemaPrinter;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Support\Facades\Validator as ValidatorFacade;
use Rebing\GraphQL\Support\Facades\GraphQL;

#[Signature('graphql:print-schema {schema=v1}')]
#[Description('Print the GraphQL schema into SDL')]
class GraphQLPrintSchemaCommand extends BaseCommand
{
    public function handle(): int
    {
        $schemaName = $this->argument('schema');

        $schema = GraphQL::schema($schemaName);

        $sdl = SchemaPrinter::doPrint($schema);

        $this->line($sdl);

        return 0;
    }

    protected function validator(): Validator
    {
        return ValidatorFacade::make($this->options(), []);
    }
}
