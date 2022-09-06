<?php

declare(strict_types=1);

namespace App\Models;

use App\Contracts\Models\Nameable;
use App\Enums\Http\Api\Filter\ComparisonOperator;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Prunable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\SoftDeletingScope;

/**
 * Class BaseModel.
 *
 * @property Carbon $created_at
 * @property Carbon $deleted_at
 * @property Carbon $updated_at
 */
abstract class BaseModel extends Model implements Nameable
{
    use HasFactory;
    use Prunable;
    use SoftDeletes;

    /**
     * The storage format of the model's date columns.
     *
     * @var string
     */
    protected $dateFormat = 'Y-m-d\TH:i:s.u';

    final public const ATTRIBUTE_CREATED_AT = Model::CREATED_AT;
    final public const ATTRIBUTE_DELETED_AT = 'deleted_at';
    final public const ATTRIBUTE_UPDATED_AT = Model::UPDATED_AT;

    /**
     * Retrieve the model for a bound value.
     *
     * @param  mixed  $value
     * @param  string|null  $field
     * @return Model|null
     *
     * @noinspection PhpMissingParentCallCommonInspection
     */
    public function resolveRouteBinding($value, $field = null): ?Model
    {
        return $this->newQuery()->where($field ?? $this->getRouteKeyName(), $value)
            ->withoutGlobalScope(SoftDeletingScope::class)
            ->first();
    }

    /**
     * Restore a soft-deleted model instance.
     *
     * @return bool|null
     */
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
     * Get the prunable model query.
     *
     * @return Builder
     */
    public function prunable(): Builder
    {
        return static::onlyTrashed()->where(BaseModel::ATTRIBUTE_DELETED_AT, ComparisonOperator::LTE, now()->subWeek());
    }
}
