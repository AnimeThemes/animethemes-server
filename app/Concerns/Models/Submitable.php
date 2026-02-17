<?php

declare(strict_types=1);

namespace App\Concerns\Models;

use App\Models\User\Submission;
use Illuminate\Database\Eloquent\Relations\MorphMany;

trait Submitable
{
    /**
     * @return MorphMany<Submission, $this>
     */
    public function submissions(): MorphMany
    {
        return $this->morphMany(Submission::class, Submission::RELATION_ACTIONABLE);
    }
}
