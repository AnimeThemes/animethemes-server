<?php

declare(strict_types=1);

namespace App\Filament\HeaderActions\Models\Wiki\Studio;

use App\Actions\Models\Wiki\Studio\AttachStudioImageAction as AttachStudioImageActionAction;
use App\Filament\HeaderActions\Models\Wiki\AttachImageHeaderAction;
use App\Models\Wiki\Studio;

/**
 * Class AttachStudioImageHeaderAction.
 */
class AttachStudioImageHeaderAction extends AttachImageHeaderAction
{
    /**
     * Initial setup for the action.
     *
     * @return void
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->action(fn (Studio $record, array $data) => (new AttachStudioImageActionAction($this->facets))->handle($record, $data));
    }
}
