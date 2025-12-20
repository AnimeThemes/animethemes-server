<?php

declare(strict_types=1);

namespace App\Observers\User\Submission;

use App\Models\User\Submission\SubmissionVirtual;
use Illuminate\Database\Eloquent\Relations\Relation;

class SubmissionVirtualObserver
{
    /**
     * Handle the SubmissionVirtual "creating" event.
     */
    public function creating(SubmissionVirtual $virtual): void
    {
        if (class_exists($model = $virtual->model_type)) {
            $virtual->model_type = Relation::getMorphAlias($model);
        }
    }
}
