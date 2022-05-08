<?php

declare(strict_types=1);

namespace App\Http\Api\Include;

use App\Http\Api\Schema\Schema;

/**
 * Class AllowedInclude.
 */
class AllowedInclude
{
    /**
     * The schema of the relation.
     *
     * @var Schema
     */
    protected readonly Schema $schema;

    /**
     * Create a new AllowedIncludePath instance.
     *
     * @param  class-string<Schema>  $schemaClass
     * @param  string  $path
     */
    final public function __construct(string $schemaClass, protected readonly string $path)
    {
        $this->schema = new $schemaClass();
    }

    /**
     * Get the schema.
     *
     * @return Schema
     */
    public function schema(): Schema
    {
        return $this->schema;
    }

    /**
     * Get the path.
     *
     * @return string
     */
    public function path(): string
    {
        return $this->path;
    }
}
