<?php

declare(strict_types=1);

namespace App\Http\Requests\Api;

use App\Contracts\Http\Api\Field\CreatableField;
use App\Http\Api\Field\Field;
use Illuminate\Support\Arr;

/**
 * Class StoreRequest.
 */
class StoreRequest extends WriteRequest
{
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
        return Arr::where(
            $this->schema()->fields(),
            fn (Field $field) => $field instanceof CreatableField
        );
    }
}
