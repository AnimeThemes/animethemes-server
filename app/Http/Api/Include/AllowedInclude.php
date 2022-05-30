<?php

declare(strict_types=1);

namespace App\Http\Api\Include;

use App\Contracts\Http\Api\Schema\SchemaInterface;

/**
 * Class AllowedInclude.
 */
class AllowedInclude
{
    /**
     * Create a new AllowedIncludePath instance.
     *
     * @param  SchemaInterface  $schema
     * @param  string  $path
     */
    final public function __construct(protected readonly SchemaInterface $schema, protected readonly string $path)
    {
    }

    /**
     * Get the schema.
     *
     * @return SchemaInterface
     */
    public function schema(): SchemaInterface
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
