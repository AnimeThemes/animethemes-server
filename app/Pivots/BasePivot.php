<?php

declare(strict_types=1);

namespace App\Pivots;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Pivot;

/**
 * Class BasePivot.
 *
 * @property Carbon $created_at
 * @property Carbon $updated_at
 */
abstract class BasePivot extends Pivot
{
    use HasFactory;

    final public const ATTRIBUTE_CREATED_AT = Model::CREATED_AT;
    final public const ATTRIBUTE_UPDATED_AT = Model::UPDATED_AT;

    /**
     * The storage format of the model's date columns.
     *
     * @var string
     */
    protected $dateFormat = 'Y-m-d\TH:i:s.u';

    /**
     * Set the keys for a select query.
     *
     * @param  Builder  $query
     * @return Builder
     *
     * @noinspection PhpMissingParentCallCommonInspection
     */
    protected function setKeysForSelectQuery($query): Builder
    {
        foreach ($this->getPrimaryKeys() as $primaryKey) {
            $query->where($primaryKey, $this->getAttribute($primaryKey));
        }

        return $query;
    }

    /**
     * Get the composite primary key for the pivot.
     *
     * @return string[]
     */
    abstract protected function getPrimaryKeys(): array;
}
