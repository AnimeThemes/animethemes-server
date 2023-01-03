<?php

declare(strict_types=1);

namespace App\Http\Middleware\Models\Wiki;

use App\Http\Middleware\Models\RecordView;

/**
 * Class RecordVideoView.
 */
class RecordVideoView extends RecordView
{
    /**
     * Get the route model binding key for the viewable object.
     *
     * @return string
     */
    protected function key(): string
    {
        return 'video';
    }
}
