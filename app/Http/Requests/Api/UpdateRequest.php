<?php

declare(strict_types=1);

namespace App\Http\Requests\Api;

use App\Contracts\Http\Api\Field\UpdatableField;
use App\Http\Api\Field\Field;
use Illuminate\Support\Arr;

/**
 * Class UpdateRequest.
 */
abstract class UpdateRequest extends WriteRequest
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
            if ($field instanceof UpdatableField) {
                $rules = $rules + [$column => $field->getUpdateRules($this)];
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
            fn (Field $field) => $field instanceof UpdatableField
        );
    }
}
