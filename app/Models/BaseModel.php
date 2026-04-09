<?php

declare(strict_types=1);

namespace App\Models;

use AjCastro\EagerLoadPivotRelations\EagerLoadPivotTrait;
use App\Concerns\Filament\ActionLogs\ModelHasActionLogs;
use App\Contracts\Models\HasSubtitle;
use App\Contracts\Models\Nameable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Str;

/**
 * @property Carbon $created_at
 * @property Carbon $updated_at
 */
abstract class BaseModel extends Model implements HasSubtitle, Nameable
{
    use EagerLoadPivotTrait;
    use ModelHasActionLogs;

    /**
     * The storage format of the model's date columns.
     *
     * @var string
     */
    protected $dateFormat = 'Y-m-d\TH:i:s.u';

    final public const ATTRIBUTE_CREATED_AT = Model::CREATED_AT;
    final public const ATTRIBUTE_UPDATED_AT = Model::UPDATED_AT;

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);

        $connectionKey = Str::of('database.models.')
            ->append(static::class)
            ->__toString();

        if (Config::has($connectionKey)) {
            $this->setConnection(Config::get($connectionKey));
        }
    }
}
