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
     * The resolver to cast the model.
     */
    public function bindResolver(array $args): null
    {
        return null;
    }
}
