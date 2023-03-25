<?php

declare(strict_types=1);

namespace App\Actions\Models;

use App\Contracts\Models\HasHashids;
use App\Models\BaseModel;
use Vinkla\Hashids\Facades\Hashids;

/**
 * Class AssignHashidsAction.
 */
class AssignHashidsAction
{
    /**
     * Assign Hashids to model.
     *
     * @param  HasHashids&BaseModel  $model
     * @param  string|null  $connection
     * @return void
     */
    public function assign(HasHashids&BaseModel $model, ?string $connection = null): void
    {
        $hashids = Hashids::connection($connection);

        $model->setAttribute(HasHashids::ATTRIBUTE_HASHID, $hashids->encode($model->hashids()));

        $model->save();
    }
}
