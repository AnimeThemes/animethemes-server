<?php

declare(strict_types=1);

namespace App\GraphQL\Definition\Queries;

use App\Contracts\GraphQL\HasFields;
use App\GraphQL\Definition\Fields\Base\DeletedAtField;
use App\GraphQL\Definition\Types\EloquentType;
use Exception;
use Illuminate\Database\Eloquent\Model;

/**
 * Class EloquentQuery.
 */
abstract class EloquentQuery extends BaseQuery
{
    /**
     * The directives of the type.
     *
     * @return array<string, array>
     */
    public function directives(): array
    {
        return [
            ...parent::directives(),

            ...($this->isTrashable() ? ['softDeletes' => []] : []),

            ...$this->canModelDirective(),
        ];
    }

    /**
     * Build the canModel directive for authorization.
     *
     * @return array
     */
    protected function canModelDirective(): array
    {
        return [
            'canModel' => [
                'ability' => 'viewAny',
                'injectArgs' => 'true',
                'model' => $this->model(),
            ],
        ];
    }

    /**
     * Get the model related to the query.
     *
     * @return class-string<Model>
     *
     * @throws Exception
     */
    public function model(): string
    {
        $baseType = $this->baseType();

        if ($baseType instanceof EloquentType) {
            return $baseType->model();
        }

        throw new Exception('The base return type must be an instance of EloquentType, '.get_class($baseType).' given.');
    }

    /**
     * Determine if the return model is trashable.
     *
     * @return bool
     */
    protected function isTrashable(): bool
    {
        $baseType = $this->baseType();

        if ($baseType instanceof EloquentType && $baseType instanceof HasFields) {
            return in_array(new DeletedAtField(), $baseType->fields());
        }

        return false;
    }
}
