<?php

declare(strict_types=1);

namespace App\Http\Requests\Api;

/**
 * Class ForceDeleteRequest.
 */
abstract class ForceDeleteRequest extends WriteRequest
{
    /**
     * The policy ability to authorize.
     *
     * @return string
     */
    protected function ability(): string
    {
        return 'forceDelete';
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
