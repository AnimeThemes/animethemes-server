<?php

declare(strict_types=1);

namespace App\Http\Requests\Api;

use App\Http\Api\Field\BooleanField;
use App\Http\Api\Field\EnumField;
use App\Http\Api\Field\Field;

abstract class WriteRequest extends BaseRequest
{
    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        parent::prepareForValidation();

        foreach ($this->getFieldsForPreparation() as $field) {
            if ($field instanceof EnumField) {
                $this->convertEnumDescriptionToValue($field->getColumn(), $field->getEnumClass());
            }
            if ($field instanceof BooleanField) {
                $this->convertBoolean($field->getColumn());
            }
        }
    }

    /**
     * Get fields for validation preparation.
     *
     * @return Field[]
     */
    protected function getFieldsForPreparation(): array
    {
        return [];
    }

    /**
     * Convert enum description parameter value to enum value.
     *
     * @param  string  $attribute
     * @param  class-string  $enumClass
     */
    protected function convertEnumDescriptionToValue(string $attribute, string $enumClass): void
    {
        $description = $this->input($attribute);
        if (is_string($description)) {
            $enumInstance = $enumClass::fromLocalizedName($description);
            if ($enumInstance !== null) {
                $this->merge([
                    $attribute => $enumInstance->value,
                ]);
            }
        }
    }

    /**
     * Convert enum parameter values.
     *
     * @param  string  $attribute
     */
    protected function convertBoolean(string $attribute): void
    {
        $booleanValue = $this->input($attribute);
        if (filter_var($booleanValue, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE) !== null) {
            $this->merge([
                $attribute => filter_var($booleanValue, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE),
            ]);
        }
    }
}
