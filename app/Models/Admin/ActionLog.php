<?php

declare(strict_types=1);

namespace App\Models\Admin;

use App\Constants\ModelConstants;
use App\Contracts\Models\Nameable;
use App\Discord\DiscordEmbedField;
use App\Enums\Models\Admin\ActionLogStatus;
use App\Models\Auth\User;
use App\Models\BaseModel;
use Filament\Actions\Action;
use Filament\Facades\Filament;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Str;
use Throwable;

/**
 * Class ActionLog.
 *
 * @property int $id
 * @property string $batch_id
 * @property string $name
 * @property string $actionable_type
 * @property int $actionable_id
 * @property string $target_type
 * @property int $target_id
 * @property string $model_type
 * @property int $model_id
 * @property string|null $exception
 * @property array|null $fields
 * @property Carbon|null $finished_at
 * @property ActionLogStatus $status
 * @property Model $target
 * @property int $user_id
 * @property User $user
 */
class ActionLog extends Model implements Nameable
{
    final public const TABLE = 'action_logs';

    final public const ATTRIBUTE_ID = 'id';
    final public const ATTRIBUTE_BATCH_ID = 'batch_id';
    final public const ATTRIBUTE_NAME = 'name';
    final public const ATTRIBUTE_USER = 'user_id';

    final public const ATTRIBUTE_ACTIONABLE = 'actionable';
    final public const ATTRIBUTE_ACTIONABLE_TYPE = 'actionable_type';
    final public const ATTRIBUTE_ACTIONABLE_ID = 'actionable_id';

    final public const ATTRIBUTE_TARGET = 'target';
    final public const ATTRIBUTE_TARGET_TYPE = 'target_type';
    final public const ATTRIBUTE_TARGET_ID = 'target_id';

    final public const ATTRIBUTE_MODEL_TYPE = 'model_type';
    final public const ATTRIBUTE_MODEL_ID = 'model_id';

    final public const ATTRIBUTE_FIELDS = 'fields';
    final public const ATTRIBUTE_STATUS = 'status';
    final public const ATTRIBUTE_EXCEPTION = 'exception';
    final public const ATTRIBUTE_FINISHED_AT = 'finished_at';

    final public const RELATION_USER = 'user';
    final public const RELATION_TARGET = 'target';

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        ActionLog::ATTRIBUTE_BATCH_ID,
        ActionLog::ATTRIBUTE_NAME,
        ActionLog::ATTRIBUTE_USER,
        ActionLog::ATTRIBUTE_ACTIONABLE_TYPE,
        ActionLog::ATTRIBUTE_ACTIONABLE_ID,
        ActionLog::ATTRIBUTE_TARGET_TYPE,
        ActionLog::ATTRIBUTE_TARGET_ID,
        ActionLog::ATTRIBUTE_MODEL_TYPE,
        ActionLog::ATTRIBUTE_MODEL_ID,
        ActionLog::ATTRIBUTE_FIELDS,
        ActionLog::ATTRIBUTE_STATUS,
        ActionLog::ATTRIBUTE_EXCEPTION,
        ActionLog::ATTRIBUTE_FINISHED_AT,
    ];

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = ActionLog::TABLE;

    /**
     * The primary key associated with the table.
     *
     * @var string
     */
    protected $primaryKey = ActionLog::ATTRIBUTE_ID;

    /**
     * Boostrap the model.
     */
    protected static function boot(): void
    {
        parent::boot();

        static::creating(function (ActionLog $actionLog) {
            if ($actionLog->status === ActionLogStatus::RUNNING) {
                Session::put('currentActionLog', $actionLog->batch_id);
            }
        });

        static::updating(function (ActionLog $actionLog) {
            if ($actionLog->status === ActionLogStatus::FINISHED || $actionLog->status === ActionLogStatus::FAILED) {
                Session::forget('currentActionLog');
            }
        });
    }

    /**
     * When an exception is thrown, the current action logs should be handled.
     */
    public static function updateCurrentActionLogToFailed(Throwable $e): void
    {
        if ($actionLog = Session::get('currentActionLog')) {
            ActionLog::query()
                ->where(ActionLog::ATTRIBUTE_BATCH_ID, $actionLog)
                ->where(ActionLog::ATTRIBUTE_STATUS, ActionLogStatus::RUNNING->value)
                ->update([
                    ActionLog::ATTRIBUTE_STATUS => ActionLogStatus::FAILED->value,
                    ActionLog::ATTRIBUTE_EXCEPTION => $e->__toString(),
                    ActionLog::ATTRIBUTE_FINISHED_AT => Date::now(),
                ]);
        }
    }

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            ActionLog::ATTRIBUTE_FIELDS => 'array',
            ActionLog::ATTRIBUTE_STATUS => ActionLogStatus::class,
        ];
    }

    /**
     * Get name.
     */
    public function getName(): string
    {
        $this->loadMissing(ActionLog::RELATION_TARGET);

        return Str::of($this->name)
            ->append(' - ')
            ->append($this->target()->getName())
            ->__toString();
    }

    /**
     * Get the actionable.
     *
     * @return MorphTo
     */
    public function actionable(): MorphTo
    {
        return $this->morphTo();
    }

    /**
     * Get the target of the action for user interface linking.
     *
     * @return MorphTo|BaseModel
     */
    public function target(): MorphTo|BaseModel
    {
        return $this->morphTo()
            ->withTrashed();
    }

    /**
     * Get the user that initiated the action.
     *
     * @return BelongsTo<User, $this>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, ActionLog::ATTRIBUTE_USER);
    }

    /**
     * Get the user id for the action log.
     */
    public static function getUserId(): int
    {
        return Filament::auth()->id();
    }

    /**
     * Register an action log for a model created.
     */
    public static function modelCreated(Model $model): ActionLog
    {
        return ActionLog::query()->create([
            ActionLog::ATTRIBUTE_BATCH_ID => Str::orderedUuid()->__toString(),
            ActionLog::ATTRIBUTE_USER => ActionLog::getUserId(),
            ActionLog::ATTRIBUTE_NAME => 'Create',
            ActionLog::ATTRIBUTE_ACTIONABLE_TYPE => Relation::getMorphAlias($model->getMorphClass()),
            ActionLog::ATTRIBUTE_ACTIONABLE_ID => $model->getKey(),
            ActionLog::ATTRIBUTE_TARGET_TYPE => Relation::getMorphAlias($model->getMorphClass()),
            ActionLog::ATTRIBUTE_TARGET_ID => $model->getKey(),
            ActionLog::ATTRIBUTE_MODEL_TYPE => Relation::getMorphAlias($model->getMorphClass()),
            ActionLog::ATTRIBUTE_MODEL_ID => $model->getKey(),
            ActionLog::ATTRIBUTE_FIELDS => static::getFields($model->getAttributes(), $model),
            ActionLog::ATTRIBUTE_STATUS => ActionLogStatus::FINISHED->value,
            ActionLog::ATTRIBUTE_FINISHED_AT => Date::now(),
        ]);
    }

    /**
     * Register an action log for a model updated.
     */
    public static function modelUpdated(Model $model): ActionLog
    {
        return ActionLog::query()->create([
            ActionLog::ATTRIBUTE_BATCH_ID => Str::orderedUuid()->__toString(),
            ActionLog::ATTRIBUTE_USER => ActionLog::getUserId(),
            ActionLog::ATTRIBUTE_NAME => 'Update',
            ActionLog::ATTRIBUTE_ACTIONABLE_TYPE => Relation::getMorphAlias($model->getMorphClass()),
            ActionLog::ATTRIBUTE_ACTIONABLE_ID => $model->getKey(),
            ActionLog::ATTRIBUTE_TARGET_TYPE => Relation::getMorphAlias($model->getMorphClass()),
            ActionLog::ATTRIBUTE_TARGET_ID => $model->getKey(),
            ActionLog::ATTRIBUTE_MODEL_TYPE => Relation::getMorphAlias($model->getMorphClass()),
            ActionLog::ATTRIBUTE_MODEL_ID => $model->getKey(),
            ActionLog::ATTRIBUTE_FIELDS => static::getFields($model->getChanges(), $model),
            ActionLog::ATTRIBUTE_STATUS => ActionLogStatus::FINISHED->value,
            ActionLog::ATTRIBUTE_FINISHED_AT => Date::now(),
        ]);
    }

    /**
     * Register an action log for a model deleted.
     */
    public static function modelDeleted(Model $model): ActionLog
    {
        return ActionLog::modelSoftDeleted('Delete', $model);
    }

    /**
     * Register an action log for a model restored.
     */
    public static function modelRestored(Model $model): ActionLog
    {
        return ActionLog::modelSoftDeleted('Restore', $model);
    }

    /**
     * Register an action log for a model that is soft-deleted.
     */
    public static function modelSoftDeleted(string $actionName, Model $model): ActionLog
    {
        return ActionLog::query()->create([
            ActionLog::ATTRIBUTE_BATCH_ID => Str::orderedUuid()->__toString(),
            ActionLog::ATTRIBUTE_USER => ActionLog::getUserId(),
            ActionLog::ATTRIBUTE_NAME => $actionName,
            ActionLog::ATTRIBUTE_ACTIONABLE_TYPE => Relation::getMorphAlias($model->getMorphClass()),
            ActionLog::ATTRIBUTE_ACTIONABLE_ID => $model->getKey(),
            ActionLog::ATTRIBUTE_TARGET_TYPE => Relation::getMorphAlias($model->getMorphClass()),
            ActionLog::ATTRIBUTE_TARGET_ID => $model->getKey(),
            ActionLog::ATTRIBUTE_MODEL_TYPE => Relation::getMorphAlias($model->getMorphClass()),
            ActionLog::ATTRIBUTE_MODEL_ID => $model->getKey(),
            ActionLog::ATTRIBUTE_FIELDS => static::getFields($model->getAttributes(), $model),
            ActionLog::ATTRIBUTE_STATUS => ActionLogStatus::FINISHED->value,
            ActionLog::ATTRIBUTE_FINISHED_AT => Date::now(),
        ]);
    }

    /**
     * Register an action log for a model attached.
     */
    public static function modelPivot(string $actionName, Model $related, Model $parent, Model $pivot, ?Action $action = null): ActionLog
    {
        $data = $action?->getData();

        return ActionLog::query()->create([
            ActionLog::ATTRIBUTE_BATCH_ID => Str::orderedUuid()->__toString(),
            ActionLog::ATTRIBUTE_USER => ActionLog::getUserId(),
            ActionLog::ATTRIBUTE_NAME => $actionName,
            ActionLog::ATTRIBUTE_ACTIONABLE_TYPE => Relation::getMorphAlias($related->getMorphClass()),
            ActionLog::ATTRIBUTE_ACTIONABLE_ID => $related->getKey(),
            ActionLog::ATTRIBUTE_TARGET_TYPE => Relation::getMorphAlias($parent->getMorphClass()),
            ActionLog::ATTRIBUTE_TARGET_ID => $parent->getKey(),
            ActionLog::ATTRIBUTE_MODEL_TYPE => Relation::getMorphAlias($pivot->getMorphClass()),
            ActionLog::ATTRIBUTE_MODEL_ID => $pivot->getKey(),
            ActionLog::ATTRIBUTE_FIELDS => $data ? static::getFields($data) : static::getFields($pivot->getAttributes(), $pivot),
            ActionLog::ATTRIBUTE_STATUS => ActionLogStatus::FINISHED->value,
            ActionLog::ATTRIBUTE_FINISHED_AT => Date::now(),
        ]);
    }

    /**
     * Register an action log for a model associated (HasMany).
     */
    public static function modelAssociated(string $actionName, Model $related, Model $parent, Action $action): ActionLog
    {
        return ActionLog::query()->create([
            ActionLog::ATTRIBUTE_BATCH_ID => Str::orderedUuid()->__toString(),
            ActionLog::ATTRIBUTE_USER => ActionLog::getUserId(),
            ActionLog::ATTRIBUTE_NAME => $actionName,
            ActionLog::ATTRIBUTE_ACTIONABLE_TYPE => Relation::getMorphAlias($related->getMorphClass()),
            ActionLog::ATTRIBUTE_ACTIONABLE_ID => $related->getKey(),
            ActionLog::ATTRIBUTE_TARGET_TYPE => Relation::getMorphAlias($parent->getMorphClass()),
            ActionLog::ATTRIBUTE_TARGET_ID => $parent->getKey(),
            ActionLog::ATTRIBUTE_MODEL_TYPE => Relation::getMorphAlias($related->getMorphClass()),
            ActionLog::ATTRIBUTE_MODEL_ID => $related->getKey(),
            ActionLog::ATTRIBUTE_FIELDS => static::getFields($action->getData()),
            ActionLog::ATTRIBUTE_STATUS => ActionLogStatus::FINISHED->value,
            ActionLog::ATTRIBUTE_FINISHED_AT => Date::now(),
        ]);
    }

    /**
     * Register an action log for when a model has an action executed.
     */
    public static function modelActioned(string $batchId, Action $action, Model $model): ActionLog
    {
        return ActionLog::query()->create([
            ActionLog::ATTRIBUTE_BATCH_ID => $batchId,
            ActionLog::ATTRIBUTE_USER => ActionLog::getUserId(),
            ActionLog::ATTRIBUTE_NAME => $action->getLabel(),
            ActionLog::ATTRIBUTE_ACTIONABLE_TYPE => Relation::getMorphAlias($model->getMorphClass()),
            ActionLog::ATTRIBUTE_ACTIONABLE_ID => $model->getKey(),
            ActionLog::ATTRIBUTE_TARGET_TYPE => Relation::getMorphAlias($model->getMorphClass()),
            ActionLog::ATTRIBUTE_TARGET_ID => $model->getKey(),
            ActionLog::ATTRIBUTE_MODEL_TYPE => Relation::getMorphAlias($model->getMorphClass()),
            ActionLog::ATTRIBUTE_MODEL_ID => $model->getKey(),
            ActionLog::ATTRIBUTE_FIELDS => static::getFields($action->getData()),
            ActionLog::ATTRIBUTE_STATUS => ActionLogStatus::RUNNING->value,
        ]);
    }

    /**
     * Update the all the models status of a batch to running.
     */
    public function batchRunning(): void
    {
        $this->query()->where(ActionLog::ATTRIBUTE_BATCH_ID, $this->batch_id)
            ->whereNotIn(ActionLog::ATTRIBUTE_STATUS, [ActionLogStatus::FINISHED->value, ActionLogStatus::FAILED->value])
            ->update([
                ActionLog::ATTRIBUTE_STATUS => ActionLogStatus::RUNNING->value,
            ]);
    }

    /**
     * Update the all the models status of a batch to finished.
     */
    public function batchFinished(): void
    {
        $this->query()->where(ActionLog::ATTRIBUTE_BATCH_ID, $this->batch_id)
            ->whereNotIn(ActionLog::ATTRIBUTE_STATUS, [ActionLogStatus::FINISHED->value, ActionLogStatus::FAILED->value])
            ->update([
                ActionLog::ATTRIBUTE_STATUS => ActionLogStatus::FINISHED->value,
                ActionLog::ATTRIBUTE_FINISHED_AT => Date::now(),
            ]);
    }

    /**
     * Update the all the models status of a batch to failed.
     */
    public function batchFailed(Throwable|string|null $exception = null): void
    {
        $this->query()->where(ActionLog::ATTRIBUTE_BATCH_ID, $this->batch_id)
            ->whereNotIn(ActionLog::ATTRIBUTE_STATUS, [ActionLogStatus::FINISHED->value, ActionLogStatus::FAILED->value])
            ->update([
                ActionLog::ATTRIBUTE_STATUS => ActionLogStatus::FAILED->value,
                ActionLog::ATTRIBUTE_EXCEPTION => $exception ? $exception->__toString() : null,
                ActionLog::ATTRIBUTE_FINISHED_AT => Date::now(),
            ]);
    }

    /**
     * Update the model status to finished.
     */
    public function finished(): void
    {
        $this->updateStatus(ActionLogStatus::FINISHED);
    }

    /**
     * Update the model status to failed.
     */
    public function failed(Throwable|string|null $exception = null): void
    {
        $this->updateStatus(ActionLogStatus::FAILED, $exception);
    }

    /**
     * Check if the model status is failed.
     */
    public function isFailed(): bool
    {
        return $this->status === ActionLogStatus::FAILED;
    }

    /**
     * Update the status of a given action event.
     */
    public function updateStatus(ActionLogStatus $status, Throwable|string|null $exception = null): void
    {
        if ($exception instanceof Throwable) {
            $exception = $exception->__toString();
        }

        $this->update([
            ActionLog::ATTRIBUTE_STATUS => $status->value,
            ActionLog::ATTRIBUTE_EXCEPTION => $exception,
            ActionLog::ATTRIBUTE_FINISHED_AT => Date::now(),
        ]);
    }

    /**
     * Format the fields to store.
     *
     * @param  array  $fields
     * @return array
     */
    protected static function getFields(array $fields, ?Model $model = null): array
    {
        return collect($fields)
            ->forget(Model::CREATED_AT)
            ->forget(Model::UPDATED_AT)
            ->forget(ModelConstants::ATTRIBUTE_DELETED_AT)
            ->map(function ($value, $key) use ($model) {
                if ($model instanceof Model) {
                    if (in_array($key, $model->getHidden())) {
                        return DiscordEmbedField::DEFAULT_FIELD_VALUE;
                    }

                    return DiscordEmbedField::formatEmbedFieldValue($model->getAttribute($key));
                }

                return DiscordEmbedField::formatEmbedFieldValue($value);
            })
            ->toArray();
    }
}
