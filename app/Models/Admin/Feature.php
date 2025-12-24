<?php

declare(strict_types=1);

namespace App\Models\Admin;

use App\Constants\FeatureConstants;
use App\Events\Admin\Feature\FeatureCreated;
use App\Events\Admin\Feature\FeatureDeleted;
use App\Events\Admin\Feature\FeatureUpdated;
use App\Models\BaseModel;
use Database\Factories\Admin\FeatureFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Carbon;

/**
 * @property Carbon $created_at
 * @property int $feature_id
 * @property string $name
 * @property string $scope
 * @property Carbon $updated_at
 * @property string $value
 *
 * @method static FeatureFactory factory(...$parameters)
 */
class Feature extends BaseModel
{
    use HasFactory;

    final public const string TABLE = 'features';

    final public const string ATTRIBUTE_ID = 'feature_id';
    final public const string ATTRIBUTE_NAME = 'name';
    final public const string ATTRIBUTE_SCOPE = 'scope';
    final public const string ATTRIBUTE_VALUE = 'value';

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
     * The event map for the model.
     *
     * Allows for object-based events for native Eloquent events.
     *
     * @var array<string, class-string>
     */
    protected $dispatchesEvents = [
        'created' => FeatureCreated::class,
        'deleted' => FeatureDeleted::class,
        'updated' => FeatureUpdated::class,
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        Feature::ATTRIBUTE_VALUE,
    ];

    public function getName(): string
    {
        return $this->name;
    }

    public function getSubtitle(): string
    {
        return $this->scope;
    }

    /**
     * Determine if the feature scope is global.
     */
    public function isNullScope(): bool
    {
        return $this->scope === FeatureConstants::NULL_SCOPE;
    }
}
