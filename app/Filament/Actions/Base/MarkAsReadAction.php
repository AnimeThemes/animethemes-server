<?php

declare(strict_types=1);

namespace App\Filament\Actions\Base;

use Filament\Actions\Action;

/**
 * Class MarkAsReadAction.
 */
class MarkAsReadAction extends Action
{
    /**
     * Initial setup for the action.
     *
     * @return void
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->name('mark-as-read');

        $this->button();

        $this->markAsRead();
    }
}
