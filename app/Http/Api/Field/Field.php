<?php

declare(strict_types=1);

namespace App\Http\Api\Field;

use App\Contracts\Http\Api\Field\FieldInterface;
use App\Http\Api\Schema\Schema;

/**
 * Class Field.
 */
abstract class Field implements FieldInterface
{
    /**
     * Create a new field instance.
     *
     * @param  Schema  $schema
     * @param  string  $key
     * @param  string|null  $column
     */
    public function __construct(
        protected readonly Schema $schema,
        protected readonly string $key,
        protected readonly ?string $column = null
    ) {}

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
     * Get the field key.
     *
     * @return string
     */
    public function getKey(): string
    {
        return $this->key;
    }

    /**
     * Get the field column.
     *
     * @return string|null
     */
    public function getColumn(): ?string
    {
        return $this->column ?? $this->key;
    }
}
