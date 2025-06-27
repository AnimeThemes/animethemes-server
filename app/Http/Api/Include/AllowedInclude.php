<?php

declare(strict_types=1);

namespace App\Http\Api\Include;

use App\Contracts\Http\Api\Schema\SchemaInterface;
use Illuminate\Support\Str;

/**
 * Class AllowedInclude.
 */
readonly class AllowedInclude
{
    /**
     * Create a new AllowedIncludePath instance.
     *
     * @param  SchemaInterface  $schema
     * @param  string  $path
     */
    final public function __construct(protected SchemaInterface $schema, protected string $path) {}

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

    /**
     * Determine if the allowed include is a direct relation for the schema.
     *
     * @return bool
     */
    public function isDirectRelation(): bool
    {
        return ! Str::of($this->path)->contains('.');
    }
}
