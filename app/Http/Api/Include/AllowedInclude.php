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
     * @param  bool|null  $allowIntermediate
     */
    final public function __construct(protected SchemaInterface $schema, protected string $path, protected ?bool $allowIntermediate = true)
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

    /**
     * Determine whether the intermediate paths are allowed.
     *
     * @return bool
     */
    public function allowsIntermediate(): bool
    {
        return $this->allowIntermediate;
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
