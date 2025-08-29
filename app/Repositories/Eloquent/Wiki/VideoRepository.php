<?php

declare(strict_types=1);

namespace App\Repositories\Eloquent\Wiki;

use App\Enums\Http\Api\Filter\ComparisonOperator;
use App\Models\Wiki\Video;
use App\Repositories\Eloquent\EloquentRepository;
use Illuminate\Database\Eloquent\Builder;

/**
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
     * Filter repository models.
     */
    public function handleFilter(string $filter, mixed $value = null): void
    {
        if ($filter === 'path') {
            $this->query->where(Video::ATTRIBUTE_PATH, ComparisonOperator::LIKE->value, "$value%");
        }
    }
}
