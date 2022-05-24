<?php

declare(strict_types=1);

namespace App\Http\Api\Field;

use App\Contracts\Http\Api\Field\FieldInterface;

/**
 * Class Field.
 */
abstract class Field implements FieldInterface
{
    /**
     * Create a new field instance.
     *
     * @param  string  $key
     * @param  string|null  $column
     */
    public function __construct(protected string $key, protected ?string $column = null)
    {
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
