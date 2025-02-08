<?php

declare(strict_types=1);

namespace App\Events;

use App\Models\Auth\User;
use App\Models\BaseModel;
use Illuminate\Support\Facades\Auth;

/**
 * Class BaseEvent.
 *
 * @template TModel of \App\Models\BaseModel
 */
abstract class BaseEvent
{
    /**
     * Create a new event instance.
     *
     * @param  TModel  $model
     */
    public function __construct(protected BaseModel $model)
    {
    }

    /**
     * Get the model that has fired this event.
     *
     * @return TModel
     */
    abstract public function getModel(): BaseModel;

    /**
     * Get the user that has fired this event.
     *
     * @return User|null
     */
    protected function getAuthenticatedUser(): ?User
    {
        return Auth::user();
    }

    /**
     * Get the user info for the footer.
     *
     * @return array
     */
    protected function getUserFooter(): array
    {
        if (is_null($this->getAuthenticatedUser())) {
            return [];
        }

        return [
            'footer' => [
                'text' => $this->getAuthenticatedUser()->getName(),
                'icon_url' => $this->getAuthenticatedUser()->getFilamentAvatarUrl(),
            ]
        ];
    }
}
