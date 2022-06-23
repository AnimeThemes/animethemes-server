<?php

declare(strict_types=1);

namespace App\Pipes;

use App\Contracts\Pipes\Pipe;
use App\Models\Auth\User;
use App\Models\BaseModel;
use App\Nova\Resources\BaseResource;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\Str;
use Laravel\Nova\Notifications\NovaNotification;

/**
 * Class BasePipe.
 *
 * @template TModel of \App\Models\BaseModel
 */
abstract class BasePipe implements Pipe
{
    /**
     * Create a new pipe instance.
     *
     * @param  TModel  $model
     */
    public function __construct(protected BaseModel $model)
    {
    }

    /**
     * Get the model passed into the pipeline.
     *
     * @return TModel
     */
    abstract public function getModel(): BaseModel;

    /**
     * Get the human-friendly label for the underlying model.
     *
     * @return string
     */
    protected function label(): string
    {
        return Str::headline(class_basename($this->getModel()));
    }

    /**
     * Send notification for user to review failure.
     *
     * @param  User  $user
     * @param  string  $message
     * @return void
     */
    protected function sendNotification(User $user, string $message): void
    {
        $resource = $this->resource();

        $uriKey = $resource::uriKey();

        $user->notify(
            NovaNotification::make()
                ->icon('flag')
                ->message($message)
                ->type(NovaNotification::WARNING_TYPE)
                ->url("/resources/$uriKey/{$this->getModel()->getKey()}")
        );
    }

    /**
     * Get the relation to resources.
     *
     * @return Relation
     */
    abstract protected function relation(): Relation;

    /**
     * Get the nova resource.
     *
     * @return class-string<BaseResource>
     */
    abstract protected function resource(): string;
}
