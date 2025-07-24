<?php

declare(strict_types=1);

namespace App\Actions\Http\Api;

use App\Models\BaseModel;
use Illuminate\Support\Str;

class ForceDeleteAction
{
    /**
     * Force delete model.
     */
    public function forceDelete(BaseModel $model): string
    {
        $message = Str::of(Str::headline(class_basename($model)))
            ->append(' \'')
            ->append($model->getName())
            ->append('\' was deleted.')
            ->__toString();

        $model->forceDelete();

        return $message;
    }
}
