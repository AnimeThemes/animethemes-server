<?php

declare(strict_types=1);

namespace App\Repositories\Eloquent\Wiki;

use App\Models\Wiki\Video;
use App\Repositories\Eloquent\EloquentRepository;
use Illuminate\Support\Collection;

/**
 * Class VideoRepository.
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
