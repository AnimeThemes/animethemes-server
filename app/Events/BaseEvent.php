<?php

declare(strict_types=1);

namespace App\Events;

use App\Contracts\Models\Nameable;
use App\Models\Auth\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

/**
 * @template TModel of \Illuminate\Database\Eloquent\Model
 */
abstract class BaseEvent
{
    /**
     * The user that fired this event.
     */
    protected ?User $authenticatedUser;

    /**
     * @param  TModel&Nameable  $model
     */
    public function __construct(protected Model&Nameable $model)
    {
        $this->authenticatedUser = Auth::user();
    }

    /**
     * Get the model that has fired this event.
     *
     * @return TModel&Nameable
     */
    abstract public function getModel(): Model&Nameable;

    /**
     * Get the user that fired this event.
     */
    protected function getAuthenticatedUser(): ?User
    {
        return $this->authenticatedUser;
    }

    /**
     * Get the user info for the footer.
     *
     * @return array<string, array<string, string>>
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
            ],
        ];
    }
}
