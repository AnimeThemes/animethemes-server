<?php

declare(strict_types=1);

namespace App\Http\Requests\Api;

use App\Contracts\Http\Api\Field\UpdatableField;

/**
 * Class UpdateRequest.
 */
abstract class UpdateRequest extends WriteRequest
{
    /**
     * The policy ability to authorize.
     *
     * @return string
     */
    protected function policyAbility(): string
    {
        return 'update';
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
            if ($field instanceof UpdatableField) {
                $rules = $rules + [$column => $field->getUpdateRules($this)];
            }
        }

        return $rules;
    }
}