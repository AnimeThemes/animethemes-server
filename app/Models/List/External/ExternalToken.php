<?php

declare(strict_types=1);

namespace App\Models\List\External;

use App\Models\Auth\User;
use App\Models\BaseModel;
use App\Models\List\ExternalProfile;
use Database\Factories\List\External\ExternalTokenFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Znck\Eloquent\Relations\BelongsToThrough;
use Znck\Eloquent\Traits\BelongsToThrough as TraitsBelongsToThrough;

/**
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
    use HasFactory;
    use TraitsBelongsToThrough;

    final public const string TABLE = 'external_tokens';

    final public const string ATTRIBUTE_ID = 'token_id';
    final public const string ATTRIBUTE_PROFILE = 'profile_id';
    final public const string ATTRIBUTE_ACCESS_TOKEN = 'access_token';
    final public const string ATTRIBUTE_REFRESH_TOKEN = 'refresh_token';

    final public const string RELATION_PROFILE = 'externalprofile';
    final public const string RELATION_USER = 'externalprofile.user';
    final public const string RELATION_USER_SHALLOW = 'user';

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
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        ExternalToken::ATTRIBUTE_ACCESS_TOKEN,
        ExternalToken::ATTRIBUTE_PROFILE,
        ExternalToken::ATTRIBUTE_REFRESH_TOKEN,
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        ExternalToken::ATTRIBUTE_ACCESS_TOKEN,
        ExternalToken::ATTRIBUTE_REFRESH_TOKEN,
    ];

    public function getName(): string
    {
        return strval($this->getKey());
    }

    public function getSubtitle(): string
    {
        return $this->externalprofile->getName();
    }

    /**
     * @return BelongsTo<ExternalProfile, $this>
     */
    public function externalprofile(): BelongsTo
    {
        return $this->belongsTo(ExternalProfile::class, ExternalToken::ATTRIBUTE_PROFILE);
    }

    /**
     * Get the user that owns the external token through the external profile.
     */
    public function user(): BelongsToThrough
    {
        return $this->belongsToThrough(
            User::class,
            ExternalProfile::class,
            null,
            '',
            [
                User::class => ExternalProfile::ATTRIBUTE_USER,
                ExternalProfile::class => ExternalProfile::ATTRIBUTE_ID,
            ]
        );
    }
}
