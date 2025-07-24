<?php

declare(strict_types=1);

namespace App\Filament\Actions\Base;

use App\Contracts\Models\SoftDeletable;
use Filament\Actions\ForceDeleteAction as BaseForceDeleteAction;

class ForceDeleteAction extends BaseForceDeleteAction
{
    /**
     * Initial setup for the action.
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->label(__('filament.actions.base.forcedelete'));

        $this->visible(fn (string $model) => in_array(SoftDeletable::class, class_implements($model)));
    }
}
