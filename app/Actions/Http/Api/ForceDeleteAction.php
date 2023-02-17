<?php

declare(strict_types=1);

namespace App\Actions\Http\Api;

use App\Models\BaseModel;
use Illuminate\Support\Str;

/**
 * Class ForceDeleteAction.
 */
class ForceDeleteAction
{
    /**
     * Force delete model.
     *
     * @param  BaseModel  $model
     * @return string
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
