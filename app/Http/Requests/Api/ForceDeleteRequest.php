<?php

declare(strict_types=1);

namespace App\Http\Requests\Api;

/**
 * Class ForceDeleteRequest.
 */
abstract class ForceDeleteRequest extends BaseRequest
{
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
