<?php

declare(strict_types=1);

namespace App\Http\Api\Criteria\Field;

use Illuminate\Support\Collection;

/**
 * Class Criteria.
 */
class Criteria
{
    /**
     * Create a new criteria instance.
     *
     * @param  string  $type
     * @param  Collection<string>  $fields
     */
    public function __construct(protected string $type, protected Collection $fields) {}

    /**
     * Get the type that this sparse fieldsets mapping belongs to.
     *
     * @return string
     */
    public function getType(): string
    {
        return $this->type;
    }

    /**
     * Get the fields to include for the type.
     *
     * @return Collection<string>
     */
    public function getFields(): Collection
    {
        return $this->fields;
    }

    /**
     * Is the field allowed?
     *
     * @param  string  $field
     * @return bool
     */
    public function isAllowedField(string $field): bool
    {
        return $this->fields->contains($field);
    }
}
