<?php

declare(strict_types=1);

namespace App\Contracts\Models;

interface SoftDeletable
{
    /**
     * Force a hard delete on a soft deleted model.
     *
     * @return bool|null
     */
    public function forceDelete();

    /**
     * Force a hard delete on a soft deleted model without raising any events.
     *
     * @return bool|null
     */
    public function forceDeleteQuietly();

    /**
     * Restore a soft-deleted model instance.
     *
     * @return bool|null
     */
    public function restore();

    /**
     * Restore a soft-deleted model instance without raising any events.
     *
     * @return bool
     */
    public function restoreQuietly();

    /**
     * Determine if the model instance has been soft-deleted.
     *
     * @return bool
     */
    public function trashed();

    /**
     * Determine if the model is currently force deleting.
     *
     * @return bool
     */
    public function isForceDeleting();
}
