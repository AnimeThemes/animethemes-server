<?php

declare(strict_types=1);

namespace App\Repositories\Eloquent\Wiki\Video;

use App\Enums\Http\Api\Filter\ComparisonOperator;
use App\Models\Wiki\Video\VideoScript;
use App\Repositories\Eloquent\EloquentRepository;
use Illuminate\Database\Eloquent\Builder;

/**
 * Class ScriptRepository.
 *
 * @extends EloquentRepository<VideoScript>
 */
class ScriptRepository extends EloquentRepository
{
    /**
     * Get the underlying query builder.
     *
     * @return Builder
     */
    protected function builder(): Builder
    {
        return VideoScript::query();
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
            $this->query->where(VideoScript::ATTRIBUTE_PATH, ComparisonOperator::LIKE->value, "$value%");
        }
    }
}
