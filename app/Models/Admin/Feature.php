<?php

declare(strict_types=1);

namespace App\Models\Admin;

use App\Constants\FeatureConstants;
use App\Events\Admin\Feature\FeatureCreated;
use App\Events\Admin\Feature\FeatureDeleted;
use App\Events\Admin\Feature\FeatureUpdated;
use App\Models\BaseModel;
use Database\Factories\Admin\FeatureFactory;
use Illuminate\Database\Eloquent\Attributes\Table;
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
#[Table(Feature::TABLE, Feature::ATTRIBUTE_ID)]
class Feature extends BaseModel
{
    use HasFactory;

    final public const string TABLE = 'features';

    final public const string ATTRIBUTE_ID = 'feature_id';
    final public const string ATTRIBUTE_NAME = 'name';
    final public const string ATTRIBUTE_SCOPE = 'scope';
    final public const string ATTRIBUTE_VALUE = 'value';

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

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            Feature::ATTRIBUTE_NAME => 'string',
            Feature::ATTRIBUTE_SCOPE => 'string',
            Feature::ATTRIBUTE_VALUE => 'string',
        ];
    }

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
