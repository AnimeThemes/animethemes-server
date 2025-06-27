<?php

declare(strict_types=1);

namespace App\Filament\Components\Filters;

use Filament\Tables\Filters\Filter;
use Illuminate\Database\Eloquent\Builder;

/**
 * Class CheckboxFilter.
 */
class CheckboxFilter extends Filter
{
    /**
     * Initial setup for the filter.
     *
     * @return void
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->checkbox();
        $this->query(fn (Builder $query): Builder => $query->where($this->getName(), true));
    }
}
