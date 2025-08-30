<?php

declare(strict_types=1);

namespace App\Http\Api\Include;

use App\Contracts\Http\Api\Schema\SchemaInterface;
use Illuminate\Support\Str;

readonly class AllowedInclude
{
    final public function __construct(protected SchemaInterface $schema, protected string $path) {}

    public function schema(): SchemaInterface
    {
        return $this->schema;
    }

    /**
     * Get the path.
     */
    public function path(): string
    {
        return $this->path;
    }

    /**
     * Determine if the allowed include is a direct relation for the schema.
     */
    public function isDirectRelation(): bool
    {
        return ! Str::of($this->path)->contains('.');
    }
}
