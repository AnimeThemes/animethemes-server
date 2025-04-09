<?php

declare(strict_types=1);

namespace App\Filament\HeaderActions\Base;

use App\Models\Admin\ActionLog;
use Filament\Actions\CreateAction as DefaultCreateAction;
use Filament\Facades\Filament;

/**
 * Class CreateHeaderAction.
 */
class CreateHeaderAction extends DefaultCreateAction
{
    /**
     * Initial setup for the action.
     *
     * @return void
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->successRedirectUrl(fn ($record) => Filament::getModelResource($record)::getUrl('view', ['record' => $record]));
        $this->after(fn ($record) => ActionLog::modelCreated($record));
    }
}
