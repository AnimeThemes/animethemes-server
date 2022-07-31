<?php

declare(strict_types=1);

namespace App\Repositories\Eloquent\Wiki;

use App\Enums\Http\Api\Filter\ComparisonOperator;
use App\Models\Wiki\Video;
use App\Repositories\Eloquent\EloquentRepository;
use Illuminate\Database\Eloquent\Builder;

/**
 * Class VideoRepository.
 *
 * @extends EloquentRepository<Video>
 */
class VideoRepository extends EloquentRepository
{
    /**
     * Get the underlying query builder.
     *
     * @return Builder
     */
    protected function builder(): Builder
    {
        return Video::query();
    }

    /**
     * Validate repository filter.
     *
     * @param  string  $filter
     * @param  mixed  $value
     * @return bool
     */
    public function validateFilter(string $filter, mixed $value = null): bool
    {
        if ($filter === 'path') {
            // Defer to source repository for validation
            return true;
        }

        return false;
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
        if ($filter === 'path') {
            $this->query->where(Video::ATTRIBUTE_PATH, ComparisonOperator::LIKE, "$value%");
        }
    }
}
