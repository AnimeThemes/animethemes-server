<?php

declare(strict_types=1);

namespace App\Http\Requests\Api;

/**
 * Class DestroyRequest.
 */
abstract class DestroyRequest extends WriteRequest
{
    /**
     * The policy ability to authorize.
     *
     * @return string
     */
    protected function policyAbility(): string
    {
        return 'delete';
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        return [];
    }
}
