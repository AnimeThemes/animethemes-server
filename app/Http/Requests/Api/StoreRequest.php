<?php

declare(strict_types=1);

namespace App\Http\Requests\Api;

use App\Contracts\Http\Api\Field\CreatableField;

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
    protected function policyAbility(): string
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
}
