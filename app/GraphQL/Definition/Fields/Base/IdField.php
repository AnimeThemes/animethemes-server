<?php

declare(strict_types=1);

namespace App\GraphQL\Definition\Fields\Base;

use App\Contracts\GraphQL\Fields\BindableField;
use App\GraphQL\Definition\Fields\IntField;
use Illuminate\Database\Eloquent\Model;

class IdField extends IntField implements BindableField
{
    /**
     * @param  class-string<Model>  $model
     */
    public function __construct(protected string $column, protected string $model)
    {
        parent::__construct($column, 'id', false);
    }

    /**
     * The description of the field.
     */
    public function description(): string
    {
        return 'The primary key of the resource';
    }

    /**
     * Get the model that the field should bind to.
     *
     * @return class-string<Model>
     */
    public function bindTo(): string
    {
        return $this->model;
    }

    /**
     * Get the column that the field should use to bind.
     */
    public function bindUsingColumn(): string
    {
        return $this->column;
    }
}
