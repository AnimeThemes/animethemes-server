<?php declare(strict_types=1);

namespace App\Repositories\Eloquent;

use App\Models\Video;
use Illuminate\Support\Collection;

/**
 * Class VideoRepository
 * @package App\Repositories\Eloquent
 */
class VideoRepository extends EloquentRepository
{
    /**
     * Get all models from the repository.
     *
     * @return Collection
     */
    public function all(): Collection
    {
        return Video::all();
    }
}
