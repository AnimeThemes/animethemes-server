<?php

declare(strict_types=1);

namespace App\GraphQL\Schema\Fields\Base;

use App\Contracts\GraphQL\Fields\BindableField;
use App\GraphQL\Filter\IntFilter;
use App\GraphQL\Schema\Fields\IntField;
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

    public function description(): string
    {
        return 'The primary key of the resource';
    }

    public function getFilter(): IntFilter
    {
        return new IntFilter($this->name(), $this->getColumn())
            ->useEq();
    }

    /**
     * The resolver to cast the model.
     */
    public function bindResolver(array $args): null
    {
        return null;
    }
}
