<?php

declare(strict_types=1);

namespace App\Models\User\Submission;

use App\Models\Auth\User;
use App\Models\BaseModel;
use App\Models\User\Submission;
use Database\Factories\User\Submission\SubmissionStageFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $stage_id
 * @property array $fields
 * @property int|null $moderator_id
 * @property User|null $moderator
 * @property string|null $moderator_notes
 * @property string|null $notes
 * @property int $stage
 * @property int $submission_id
 * @property Submission $submission
 *
 * @method static SubmissionStageFactory factory(...$parameters)
 */
class SubmissionStage extends BaseModel
{
    use HasFactory;

    final public const string TABLE = 'submission_stage';

    final public const string ATTRIBUTE_ID = 'stage_id';

    final public const string ATTRIBUTE_FIELDS = 'fields';
    final public const string ATTRIBUTE_MODERATOR = 'moderator_id';
    final public const string ATTRIBUTE_MODERATOR_NOTES = 'moderator_notes';
    final public const string ATTRIBUTE_NOTES = 'notes';
    final public const string ATTRIBUTE_STAGE = 'stage';
    final public const string ATTRIBUTE_SUBMISSION = 'submission_id';

    final public const string RELATION_MODERATOR = 'moderator';
    final public const string RELATION_SUBMISSION = 'submission';

    /**
     * Is auditing disabled?
     *
     * @var bool
     */
    public static $auditingDisabled = true;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        SubmissionStage::ATTRIBUTE_FIELDS,
        SubmissionStage::ATTRIBUTE_MODERATOR,
        SubmissionStage::ATTRIBUTE_MODERATOR_NOTES,
        SubmissionStage::ATTRIBUTE_NOTES,
        SubmissionStage::ATTRIBUTE_STAGE,
        SubmissionStage::ATTRIBUTE_SUBMISSION,
    ];

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = SubmissionStage::TABLE;

    /**
     * The primary key associated with the table.
     *
     * @var string
     */
    protected $primaryKey = SubmissionStage::ATTRIBUTE_ID;

    public function getName(): string
    {
        return strval($this->getKey());
    }

    public function getSubtitle(): string
    {
        return strval($this->getKey());
    }

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            SubmissionStage::ATTRIBUTE_FIELDS => 'array',
            SubmissionStage::ATTRIBUTE_STAGE => 'int',
        ];
    }

    /**
     * @return BelongsTo<Submission, $this>
     */
    public function submission(): BelongsTo
    {
        return $this->belongsTo(Submission::class, SubmissionStage::ATTRIBUTE_SUBMISSION);
    }
}
