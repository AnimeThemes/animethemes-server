<?php

declare(strict_types=1);

namespace App\Filament\BulkActions;

use App\Models\BaseModel;
use Filament\Support\Enums\MaxWidth;
use Filament\Tables\Actions\BulkAction;
use Illuminate\Database\Eloquent\Collection;

/**
 * Class BaseBulkAction.
 *
 * Bulk actions are present in the table to perform an action on more than one model at once.
 */
abstract class BaseBulkAction extends BulkAction
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

        $this->action(fn (Collection $records, array $data) => $this->handle($records, $data));
    }

    /**
     * Handle the action.
     *
     * @param  Collection<int, BaseModel>  $records
     * @param  array  $fields
     * @return void
     */
    abstract public function handle(Collection $records, array $fields): void;
}
