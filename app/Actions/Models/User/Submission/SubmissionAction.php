<?php

declare(strict_types=1);

namespace App\Actions\Models\User\Submission;

use App\Enums\Models\User\ApprovableStatus;
use App\Enums\Models\User\SubmissionActionType;
use App\Models\User\Submission;
use App\Models\User\Submission\SubmissionStep;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Pivot;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;

class SubmissionAction
{
    /**
     * Create a submission with the given steps.
     *
     * @param  SubmissionStep|SubmissionStep[]  $steps
     */
    public static function makeSubmission(SubmissionStep|array $steps, ?string $notes = null): Submission
    {
        $submission = Submission::query()->create([
            Submission::ATTRIBUTE_USER => Auth::id(),
            Submission::ATTRIBUTE_STATUS => ApprovableStatus::PENDING->value,
            Submission::ATTRIBUTE_NOTES => $notes,
        ]);

        $submission->steps()->saveMany(Arr::wrap($steps));

        return $submission;
    }

    /**
     * Create a submission step to create a model.
     *
     * @param  class-string<Model>  $model
     */
    public static function makeForCreate(string $model, array $fields): SubmissionStep
    {
        return static::makeFor(SubmissionActionType::CREATE, $model, $fields);
    }

    /**
     * Create a submission step to edit a model.
     */
    public static function makeForUpdate(Model $model, array $fields): SubmissionStep
    {
        return static::makeFor(SubmissionActionType::UPDATE, $model, $fields);
    }

    /**
     * Create a submission step to delete a model.
     */
    public static function makeForDelete(Model $model): SubmissionStep
    {
        return static::makeFor(SubmissionActionType::DELETE, $model);
    }

    /**
     * Create a submission step to attach a model to another in a many-to-many relationship.
     *
     * @param  class-string<Pivot>  $pivot
     */
    public static function makeForAttach(Model $foreign, Model $related, string $pivot, array $fields): SubmissionStep
    {
        return static::makeFor(SubmissionActionType::ATTACH, $foreign, $fields, $related, $pivot);
    }

    /**
     * Create a submission step to detach a model from another in a many-to-many relationship.
     */
    public static function makeForDetach(Model $foreign, Model $related, Pivot $pivot, array $fields): SubmissionStep
    {
        return static::makeFor(SubmissionActionType::DETACH, $foreign, $fields, $related, $pivot);
    }

    /**
     * Create a submission step for given action.
     *
     * @param  class-string<Model>|Model  $model
     */
    protected static function makeFor(SubmissionActionType $action, Model|string $model, ?array $fields = null, ?Model $related = null, Pivot|string|null $pivot = null): SubmissionStep
    {
        return new SubmissionStep([
            SubmissionStep::ATTRIBUTE_ACTION => $action->value,
            SubmissionStep::ATTRIBUTE_ACTIONABLE_TYPE => $model instanceof Model ? Relation::getMorphAlias($model->getMorphClass()) : $model,
            SubmissionStep::ATTRIBUTE_ACTIONABLE_ID => $model instanceof Model ? $model->getKey() : null,
            SubmissionStep::ATTRIBUTE_FIELDS => Arr::where($fields, fn ($value, $key) => $model->isFillable($key)),
            SubmissionStep::ATTRIBUTE_STATUS => ApprovableStatus::PENDING->value,
            SubmissionStep::ATTRIBUTE_TARGET_TYPE => $related instanceof Model ? Relation::getMorphAlias($related->getMorphClass()) : null,
            SubmissionStep::ATTRIBUTE_TARGET_ID => $related instanceof Model ? $related->getKey() : null,
            SubmissionStep::ATTRIBUTE_PIVOT => $pivot instanceof Model ? Relation::getMorphAlias($pivot->getMorphClass()) : $pivot,
        ]);
    }
}
