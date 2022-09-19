<?php

declare(strict_types=1);

namespace App\Repositories\Eloquent\Admin;

use App\Models\Admin\Dump;
use App\Repositories\Eloquent\EloquentRepository;
use Illuminate\Database\Eloquent\Builder;

/**
 * Class DumpRepository.
 *
 * @extends EloquentRepository<Dump>
 */
class DumpRepository extends EloquentRepository
{
    /**
     * Get the underlying query builder.
     *
     * @return Builder
     */
    protected function builder(): Builder
    {
        return Dump::query();
    }

    /**
     * Filter repository models.
     *
     * @param  string  $filter
     * @param  mixed  $value
     * @return void
     */
    public function handleFilter(string $filter, mixed $value = null): void
    {
        // not supported
    }
}
