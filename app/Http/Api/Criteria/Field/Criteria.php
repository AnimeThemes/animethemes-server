<?php

declare(strict_types=1);

namespace App\Http\Api\Criteria\Field;

use Illuminate\Support\Collection;

readonly class Criteria
{
    /**
     * @param  Collection<int, string>  $fields
     */
    public function __construct(protected string $type, protected Collection $fields) {}

    /**
     * Get the type that this sparse fieldsets mapping belongs to.
     */
    public function getType(): string
    {
        return $this->type;
    }

    /**
     * Get the fields to include for the type.
     *
     * @return Collection<int, string>
     */
    public function getFields(): Collection
    {
        return $this->fields;
    }

    /**
     * Is the field allowed?
     */
    public function isAllowedField(string $field): bool
    {
        return $this->fields->contains($field);
    }
}
