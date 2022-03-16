<?php

declare(strict_types=1);

namespace App\Http\Requests\Api;

/**
 * Class RestoreRequest.
 */
abstract class RestoreRequest extends WriteRequest
{
    /**
     * The ability to authorize.
     *
     * @return string
     */
    protected function ability(): string
    {
        return 'restore';
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
