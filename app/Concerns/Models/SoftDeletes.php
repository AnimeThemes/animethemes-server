<?php

declare(strict_types=1);

namespace App\Concerns\Models;

use App\Constants\ModelConstants;
use App\Enums\Http\Api\Filter\ComparisonOperator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Prunable;
use Illuminate\Database\Eloquent\SoftDeletes as BaseSoftDeletes;
use Illuminate\Support\Carbon;

/**
 * @property Carbon $deleted_at
 */
trait SoftDeletes
{
    use BaseSoftDeletes;
    use Prunable;

    public const ATTRIBUTE_DELETED_AT = ModelConstants::ATTRIBUTE_DELETED_AT;

    public function restore(): ?bool
    {
        // If the restoring event does not return false, we will proceed with this
        // restore operation. Otherwise, we bail out so the developer will stop
        // the restore totally. We will clear the deleted timestamp and save.
        if ($this->fireModelEvent('restoring') === false) {
            return false;
        }

        $this->{$this->getDeletedAtColumn()} = null;

        // Once we have saved the model, we will fire the "restored" event so this
        // developer will do anything they need to after a restore operation is
        // totally finished. Then we will return the result of the save call.
        $this->exists = true;

        // Save quietly so that we do not fire an updated event on restore
        $result = $this->saveQuietly();

        $this->fireModelEvent('restored', false);

        return $result;
    }

    /**
     * @return Builder
     */
    public function prunable(): Builder
    {
        return static::onlyTrashed()->where(
            self::ATTRIBUTE_DELETED_AT,
            ComparisonOperator::LTE->value,
            now()->subWeek()
        );
    }
}
