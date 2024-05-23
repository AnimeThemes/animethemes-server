<?php

declare(strict_types=1);

namespace App\Models\Admin;

use App\Constants\FeatureConstants;
use App\Events\Admin\Feature\FeatureCreated;
use App\Events\Admin\Feature\FeatureDeleted;
use App\Events\Admin\Feature\FeatureUpdated;
use Database\Factories\Admin\FeatureFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;
use Laravel\Nova\Actions\Actionable;

/**
 * Class Feature.
 *
 * @property Carbon $created_at
 * @property int $feature_id
 * @property string $name
 * @property string $scope
 * @property Carbon $updated_at
 * @property string $value
 *
 * @method static FeatureFactory factory(...$parameters)
 */
class Feature extends Model
{
    use Actionable;
    use HasFactory;

    final public const TABLE = 'features';

    final public const ATTRIBUTE_ID = 'feature_id';
    final public const ATTRIBUTE_NAME = 'name';
    final public const ATTRIBUTE_SCOPE = 'scope';
    final public const ATTRIBUTE_VALUE = 'value';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        Feature::ATTRIBUTE_VALUE,
    ];

    /**
     * The event map for the model.
     *
     * Allows for object-based events for native Eloquent events.
     *
     * @var array
     */
    protected $dispatchesEvents = [
        'created' => FeatureCreated::class,
        'deleted' => FeatureDeleted::class,
        'updated' => FeatureUpdated::class,
    ];

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = Feature::TABLE;

    /**
     * The primary key associated with the table.
     *
     * @var string
     */
    protected $primaryKey = Feature::ATTRIBUTE_ID;

    /**
     * Get name.
     *
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * Get subtitle.
     *
     * @return string
     */
    public function getSubtitle(): string
    {
        return $this->scope;
    }

    /**
     * Determine if the feature scope is global.
     *
     * @return bool
     */
    public function isNullScope(): bool
    {
        return $this->scope === FeatureConstants::NULL_SCOPE;
    }
}
