<?php

declare(strict_types=1);

namespace App\Http\Requests\Api;

use App\Contracts\Http\Api\Field\CreatableField;
use App\Http\Api\Field\Field;

/**
 * Class StoreRequest.
 */
abstract class StoreRequest extends WriteRequest
{
    /**
     * The policy ability to authorize.
     *
     * @return string
     */
    protected function ability(): string
    {
        return 'create';
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        $rules = [];

        foreach ($this->schema()->fields() as $field) {
            $column = $field->getColumn();
            if ($field instanceof CreatableField) {
                $rules = $rules + [$column => $field->getCreationRules($this)];
            }
        }

        return $rules;
    }

    /**
     * Get fields for validation preparation.
     *
     * @return Field[]
     *
     * @noinspection PhpMissingParentCallCommonInspection
     */
    protected function getFieldsForPreparation(): array
    {
        return collect($this->schema()->fields())
            ->filter(fn (Field $field) => $field instanceof CreatableField)
            ->all();
    }
}
