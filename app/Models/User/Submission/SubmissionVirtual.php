<?php

declare(strict_types=1);

namespace App\Models\User\Submission;

use App\Models\Auth\User;
use App\Models\BaseModel;
use App\Observers\User\Submission\SubmissionVirtualObserver;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\Relation;

/**
 * @property int $submission_virtual_id
 * @property bool $exists
 * @property array $fields
 * @property class-string<Model> $model
 * @property string $model_type
 * @property int $user_id
 * @property User $user
 */
#[ObservedBy(SubmissionVirtualObserver::class)]
class SubmissionVirtual extends BaseModel
{
    final public const string TABLE = 'submission_virtuals';

    final public const string ATTRIBUTE_ID = 'submission_virtual_id';

    final public const string ATTRIBUTE_EXISTS = 'exists';
    final public const string ATTRIBUTE_FIELDS = 'fields';
    final public const string ATTRIBUTE_MODEL_TYPE = 'model_type';
    final public const string ATTRIBUTE_USER = 'user_id';

    final public const string RELATION_USER = 'user';

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = SubmissionVirtual::TABLE;

    /**
     * The primary key associated with the table.
     *
     * @var string
     */
    protected $primaryKey = SubmissionVirtual::ATTRIBUTE_ID;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        SubmissionVirtual::ATTRIBUTE_EXISTS,
        SubmissionVirtual::ATTRIBUTE_FIELDS,
        SubmissionVirtual::ATTRIBUTE_MODEL_TYPE,
        SubmissionVirtual::ATTRIBUTE_USER,
    ];

    protected $appends = [
        'model',
    ];

    public function getName(): string
    {
        return strval($this->getKey());
    }

    public function getSubtitle(): string
    {
        return strval($this->getKey());
    }

    /**
     * @return class-string<Model>
     */
    protected function getModelAttribute(): string
    {
        return Relation::getMorphedModel($this->model_type) ?? $this->model_type;
    }

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            SubmissionVirtual::ATTRIBUTE_EXISTS => 'boolean',
            SubmissionVirtual::ATTRIBUTE_FIELDS => 'array',
        ];
    }

    /**
     * @return BelongsTo<User, $this>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, SubmissionVirtual::ATTRIBUTE_USER);
    }
}
