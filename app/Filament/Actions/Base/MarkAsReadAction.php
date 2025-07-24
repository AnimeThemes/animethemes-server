<?php

declare(strict_types=1);

namespace App\Filament\Actions\Base;

use Filament\Actions\Action;

class MarkAsReadAction extends Action
{
    /**
     * Initial setup for the action.
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->name('mark-as-read');

        $this->button();

        $this->markAsRead();
    }
}
