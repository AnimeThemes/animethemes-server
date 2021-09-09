<?php

declare(strict_types=1);

namespace App\Http\Requests\Api;

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
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        return array_merge(
            $this->getIncludeRules(),
            $this->getPagingRules(),
            $this->getSearchRules(),
            $this->getSortRules(),
        );
    }

    /**
     * Get the include validation rules.
     *
     * @return array
     */
    abstract protected function getIncludeRules(): array;

    /**
     * Get the paging validation rules.
     *
     * @return array
     */
    abstract protected function getPagingRules(): array;

    /**
     * Get the search validation rules.
     *
     * @return array
     */
    abstract protected function getSearchRules(): array;

    /**
     * Get the sort validation rules.
     *
     * @return array
     */
    abstract protected function getSortRules(): array;
}
