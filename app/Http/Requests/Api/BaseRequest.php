<?php

declare(strict_types=1);

namespace App\Http\Requests\Api;

use App\Http\Api\Query\Query;
use App\Http\Api\Schema\Schema;
use Illuminate\Foundation\Http\FormRequest;

/**
 * Class BaseRequest.
 */
abstract class BaseRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    abstract public function authorize(): bool;

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    abstract public function rules(): array;

    /**
     * Get the schema.
     *
     * @return Schema
     */
    abstract protected function schema(): Schema;

    /**
     * Get the validation API Query.
     *
     * @return Query
     */
    abstract public function getQuery(): Query;
}
