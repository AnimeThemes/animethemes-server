<?php

declare(strict_types=1);

namespace App\Filament\TableActions;

use Filament\Support\Enums\MaxWidth;
use Filament\Tables\Actions\Action;

/**
 * Class BaseTableAction.
 */
abstract class BaseTableAction extends Action
{
    /**
     * Initial setup for the action.
     *
     * @return void
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->requiresConfirmation();

        $this->modalWidth(MaxWidth::FourExtraLarge);

        $this->action(fn (array $data) => $this->handle($data));
    }

    /**
     * Perform the action on the table.
     *
     * @param  array  $fields
     * @return void
     */
    abstract public function handle(array $fields): void;
}
