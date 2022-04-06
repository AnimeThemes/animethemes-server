<?php

declare(strict_types=1);

namespace App\Models\Auth;

use Carbon\Carbon;
use Database\Factories\Auth\TeamFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Collection;
use Laravel\Jetstream\Events\TeamCreated;
use Laravel\Jetstream\Events\TeamDeleted;
use Laravel\Jetstream\Events\TeamUpdated;
use Laravel\Jetstream\Team as JetstreamTeam;

/**
 * Class Team.
 *
 * @property Carbon $created_at
 * @property int $id
 * @property string $name
 * @property User $owner
 * @property bool $personal_team
 * @property Collection $teamInvitations
 * @property Carbon $updated_at
 * @property int $user_id
 * @property Collection $users
 *
 * @method static TeamFactory factory(...$parameters)
 */
class Team extends JetstreamTeam
{
    use HasFactory;

    final public const TABLE = 'teams';

    final public const ATTRIBUTE_ID = 'id';
    final public const ATTRIBUTE_NAME = 'name';
    final public const ATTRIBUTE_PERSONAL_TEAM = 'personal_team';
    final public const ATTRIBUTE_USER = 'user_id';

    /**
     * The attributes that are mass assignable.
     *
     * @var string[]
     */
    protected $fillable = [
        Team::ATTRIBUTE_NAME,
        Team::ATTRIBUTE_PERSONAL_TEAM,
    ];

    /**
     * The event map for the model.
     *
     * @var array
     */
    protected $dispatchesEvents = [
        'created' => TeamCreated::class,
        'deleted' => TeamDeleted::class,
        'updated' => TeamUpdated::class,
    ];

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = Team::TABLE;

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        Team::ATTRIBUTE_PERSONAL_TEAM => 'boolean',
    ];

    /**
     * The storage format of the model's date columns.
     *
     * @var string
     */
    protected $dateFormat = 'Y-m-d\TH:i:s.u';
}
