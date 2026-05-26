<?php

declare(strict_types=1);

namespace App\Models;

use App\Contracts\Models\HasSubtitle;
use App\Contracts\Models\Nameable;
use App\Models\Admin\Activity;
use Illuminate\Database\Eloquent\Attributes\DateFormat;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Str;

/**
 * @property Carbon $created_at
 * @property Carbon $updated_at
 */
#[DateFormat('Y-m-d\TH:i:s.u')]
abstract class BaseModel extends Model implements HasSubtitle, Nameable
{
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

    /** @return MorphMany<Activity, $this> */
    public function activities(): MorphMany
    {
        return $this->morphMany(Activity::class, 'subject');
    }
}
