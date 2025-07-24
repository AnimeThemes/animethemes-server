<?php

declare(strict_types=1);

namespace App\Concerns\Http\Requests\Api;

use Illuminate\Validation\Validator;

trait ValidatesConditionally
{
    /**
     * Configure the validator instance.
     * Note: This function is invoked by name if it exists.
     * We define a decorator that provides better clarity for what conditional validation needs to be applied.
     *
     * @noinspection PhpUnused
     */
    public function withValidator(Validator $validator): void
    {
        $this->handleConditionalValidation($validator);
    }

    /**
     * Configure conditional validation.
     */
    protected function handleConditionalValidation(Validator $validator): void
    {
        $this->conditionallyRestrictAllowedFilterValues($validator);
    }

    /**
     * Filters shall be validated based on values.
     * If the value contains a separator, we assume this is a multi-value filter that builds a where in clause.
     * Otherwise, we assume this is a single-value filter that builds a where clause.
     * Logical operators apply to specific clauses, so we must check formatted filter parameters against filter values.
     */
    protected function conditionallyRestrictAllowedFilterValues(Validator $validator): void
    {
        //
    }
}
