<?php

declare(strict_types=1);

namespace App\Contracts\Events;

use Illuminate\Contracts\Events\ShouldHandleEventsAfterCommit;

interface UpdateAnimeSynonymsEvent extends ShouldHandleEventsAfterCommit
{
    public function updateAnimeSynonyms(): void;
}
