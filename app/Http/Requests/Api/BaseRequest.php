<?php

declare(strict_types=1);

namespace App\Http\Requests\Api;

use App\Contracts\Http\Api\InteractsWithSchema;
use App\Http\Api\Schema\Schema;
use Illuminate\Foundation\Http\FormRequest;
use RuntimeException;

abstract class BaseRequest extends FormRequest
{
    /**
     * The underlying schema used to perform validation.
     *
     * @var Schema
     */
    protected Schema $schema;

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    abstract public function rules(): array;

    /**
     * Get the underlying schema.
     *
     * @return Schema
     */
    public function schema(): Schema
    {
        return $this->schema;
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        parent::prepareForValidation();

        $controller = $this->route()->getController();

        if (! $controller instanceof InteractsWithSchema) {
            throw new RuntimeException("Cannot resolve schema for controller '{$this->route()->getControllerClass()}'");
        }

        $this->schema = $controller->schema();
    }
}
