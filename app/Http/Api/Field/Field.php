<?php

declare(strict_types=1);

namespace App\Http\Api\Field;

use App\Contracts\Http\Api\Field\FieldInterface;
use App\Http\Api\Schema\Schema;

abstract class Field implements FieldInterface
{
    public function __construct(
        protected readonly Schema $schema,
        protected readonly string $key,
        protected readonly ?string $column = null
    ) {}

    /**
     * Get the schema.
     */
    public function schema(): Schema
    {
        return $this->schema;
    }

    public function getKey(): string
    {
        return $this->key;
    }

    /**
     * Get the field column.
     */
    public function getColumn(): ?string
    {
        return $this->column ?? $this->key;
    }
}
