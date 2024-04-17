<?php

declare(strict_types=1);

namespace App\Filament\Actions\Models\Wiki\Studio;

use App\Actions\Models\Wiki\Studio\AttachStudioImageAction as AttachStudioImageActionAction;
use App\Filament\Actions\Models\Wiki\AttachImageAction;
use Illuminate\Database\Eloquent\Model;

/**
 * Class AttachStudioImageAction.
 */
class AttachStudioImageAction extends AttachImageAction
{
    /**
     * Initial setup for the action.
     *
     * @return void
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->action(fn (Model $record, array $data) => (new AttachStudioImageActionAction($this->facets))->handle($record, $data));
    }
}
