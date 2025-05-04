<?php

declare(strict_types=1);

namespace App\Models\User;

use App\Contracts\Models\HasSubtitle;
use App\Contracts\Models\Nameable;
use App\Enums\Models\User\ApprovableStatus;
use App\Models\User\Report\ReportStep;
use App\Models\Auth\User;
use Database\Factories\User\ReportFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;

/**
 * Class Report.
 *
 * @property Carbon|null $finished_at
 * @property User|null $moderator
 * @property int|null $moderator_id
 * @property string|null $mod_notes
 * @property string|null $notes
 * @property ApprovableStatus $status
 * @property Collection<int, ReportStep> $steps
 * @property User|null $user
 * @property int|null $user_id
 *
 * @method static Builder pending()
 * @method static ReportFactory factory(...$parameters)
 */
class Report extends Model implements Nameable, HasSubtitle
{
    use HasFactory;

    final public const TABLE = 'reports';

    final public const ATTRIBUTE_ID = 'report_id';
    final public const ATTRIBUTE_FINISHED_AT = 'finished_at';
    final public const ATTRIBUTE_MODERATOR = 'moderator_id';
    final public const ATTRIBUTE_MOD_NOTES = 'mod_notes';
    final public const ATTRIBUTE_NOTES = 'notes';
    final public const ATTRIBUTE_STATUS = 'status';
    final public const ATTRIBUTE_USER = 'user_id';

    final public const RELATION_MODERATOR = 'moderator';
    final public const RELATION_STEPS = 'steps';
    final public const RELATION_USER = 'user';

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        Report::ATTRIBUTE_FINISHED_AT,
        Report::ATTRIBUTE_MODERATOR,
        Report::ATTRIBUTE_MOD_NOTES,
        Report::ATTRIBUTE_NOTES,
        Report::ATTRIBUTE_STATUS,
        Report::ATTRIBUTE_USER,
    ];

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = Report::TABLE;

    /**
     * The primary key associated with the table.
     *
     * @var string
     */
    protected $primaryKey = Report::ATTRIBUTE_ID;

    /**
     * Get the route key for the model.
     *
     * @return string
     *
     * @noinspection PhpMissingParentCallCommonInspection
     */
    public function getRouteKeyName(): string
    {
        return Report::ATTRIBUTE_ID;
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
        if ($user = $this->user) {
            return $user->getName();
        }

        return strval($this->getKey());
    }

    /**
     * Get the eager loads needed to the subtitle.
     *
     * @return array
     */
    public static function getEagerLoadsForSubtitle(): array
    {
        return [
            Report::RELATION_USER,
        ];
    }

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            Report::ATTRIBUTE_FINISHED_AT => 'datetime',
            Report::ATTRIBUTE_STATUS => ApprovableStatus::class,
        ];
    }

    /**
     * Scope a query to only include pending reports.
     *
     * @param  Builder  $query
     * @return void
     */
    public function scopePending(Builder $query): void
    {
        $query->where(Report::ATTRIBUTE_STATUS, ApprovableStatus::PENDING->value);
    }

    /**
     * Get the steps of the report.
     *
     * @return HasMany<ReportStep, $this>
     */
    public function steps(): HasMany
    {
        return $this->hasMany(ReportStep::class, ReportStep::ATTRIBUTE_REPORT);
    }

    /**
     * Get the moderator that is working on the report.
     *
     * @return BelongsTo<User, $this>
     */
    public function moderator(): BelongsTo
    {
        return $this->belongsTo(User::class, Report::ATTRIBUTE_MODERATOR);
    }

    /**
     * Get the user that made the report.
     *
     * @return BelongsTo<User, $this>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, Report::ATTRIBUTE_USER);
    }
}
