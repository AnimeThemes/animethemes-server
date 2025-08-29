<?php

declare(strict_types=1);

namespace App\Actions\Models;

use App\Contracts\Models\HasHashids;
use App\Models\BaseModel;
use Vinkla\Hashids\Facades\Hashids;

class AssignHashidsAction
{
    public function assign(HasHashids&BaseModel $model, ?string $connection = null): void
    {
        $hashids = Hashids::connection($connection);

        $model->setAttribute(HasHashids::ATTRIBUTE_HASHID, $hashids->encode($model->hashids()));

        $model->save();
    }
}
