<?php

declare(strict_types=1);

namespace App\Models\List\External;

use App\Events\List\ExternalProfile\ExternalToken\ExternalTokenCreated;
use App\Events\List\ExternalProfile\ExternalToken\ExternalTokenDeleted;
use App\Events\List\ExternalProfile\ExternalToken\ExternalTokenRestored;
use App\Events\List\ExternalProfile\ExternalToken\ExternalTokenUpdated;
use App\Models\Auth\User;
use App\Models\BaseModel;
use App\Models\List\ExternalProfile;
use Database\Factories\List\External\ExternalTokenFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Znck\Eloquent\Relations\BelongsToThrough;
use Znck\Eloquent\Traits\BelongsToThrough as TraitsBelongsToThrough;

/**
 * Class ExternalToken.
 *
 * @property int $token_id
 * @property int $profile_id
 * @property string|null $access_token
 * @property ExternalProfile $externalprofile
 * @property int $profile_id
 * @property string|null $refresh_token
 * @property User $user
 *
 * @method static ExternalTokenFactory factory(...$parameters)
 */
class ExternalToken extends BaseModel
{
    use TraitsBelongsToThrough;

    final public const TABLE = 'external_tokens';

    final public const ATTRIBUTE_ID = 'token_id';
    final public const ATTRIBUTE_PROFILE = 'profile_id';
    final public const ATTRIBUTE_ACCESS_TOKEN = 'access_token';
    final public const ATTRIBUTE_REFRESH_TOKEN = 'refresh_token';

    final public const RELATION_PROFILE = 'externalprofile';
    final public const RELATION_USER = 'externalprofile.user';
    final public const RELATION_USER_SHALLOW = 'user';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        ExternalToken::ATTRIBUTE_ACCESS_TOKEN,
        ExternalToken::ATTRIBUTE_PROFILE,
        ExternalToken::ATTRIBUTE_REFRESH_TOKEN,
    ];

    /**
     * The event map for the model.
     *
     * Allows for object-based events for native Eloquent events.
     *
     * @var array
     */
    protected $dispatchesEvents = [
        'created' => ExternalTokenCreated::class,
        'deleted' => ExternalTokenDeleted::class,
        'restored' => ExternalTokenRestored::class,
        'updated' => ExternalTokenUpdated::class,
    ];

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = ExternalToken::TABLE;

    /**
     * The primary key associated with the table.
     *
     * @var string
     */
    protected $primaryKey = ExternalToken::ATTRIBUTE_ID;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            ExternalToken::ATTRIBUTE_ACCESS_TOKEN => 'hashed',
            ExternalToken::ATTRIBUTE_REFRESH_TOKEN => 'hashed',
        ];
    }

    /**
     * Get name.
     *
     * @return string
     */
    public function getName(): string
    {
        return strval($this->getKey());
    }

    /**
     * Get subtitle.
     *
     * @return string
     */
    public function getSubtitle(): string
    {
        return $this->profile->getName();
    }

    /**
     * Get the route key for the model.
     *
     * @return string
     *
     * @noinspection PhpMissingParentCallCommonInspection
     */
    public function getRouteKeyName(): string
    {
        return ExternalToken::ATTRIBUTE_ID;
    }

    /**
     * Get the external profile that owns the external token.
     *
     * @return BelongsTo
     */
    public function externalprofile(): BelongsTo
    {
        return $this->belongsTo(ExternalProfile::class, ExternalToken::ATTRIBUTE_PROFILE);
    }

    /**
     * Get the user that owns the external token through the external profile.
     *
     * @return BelongsToThrough
     */
    public function user(): BelongsToThrough
    {
        return $this->belongsToThrough(
            User::class,
            ExternalProfile::class,
            null,
            '',
            [
                User::class => User::ATTRIBUTE_ID,
                ExternalProfile::class => ExternalProfile::ATTRIBUTE_ID,
            ]
        );
    }
}
