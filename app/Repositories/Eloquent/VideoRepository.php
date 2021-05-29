<?php

namespace App\Repositories\Eloquent;

use App\Models\Video;

class VideoRepository extends EloquentRepository
{
    /**
     * Get all models from the repository.
     *
     * @return \Illuminate\Support\Collection
     */
    public function all()
    {
        return Video::all();
    }
}
