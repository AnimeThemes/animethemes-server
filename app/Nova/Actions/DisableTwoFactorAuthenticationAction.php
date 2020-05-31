<?php

namespace App\Nova\Actions;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Collection;
use Laravel\Nova\Actions\Action;
use Laravel\Nova\Fields\ActionFields;

class DisableTwoFactorAuthenticationAction extends Action
{
    use InteractsWithQueue, Queueable;

    /**
     * Get the displayable name of the action.
     *
     * @return string
     */
    public function name()
    {
        return __('nova.2fa_disable_action');
    }

    /**
     * Perform the action on the given models.
     *
     * @param  \Laravel\Nova\Fields\ActionFields  $fields
     * @param  \Illuminate\Support\Collection  $models
     * @return mixed
     */
    public function handle(ActionFields $fields, Collection $models)
    {
        $disabled_users = [];

        foreach ($models as $model) {
            // Only disable 2FA for users that have enabled it
            if ($model->hasTwoFactorEnabled()) {
                $model->disableTwoFactorAuth();
                array_push($disabled_users, $model->name);
            }
        }

        if (!empty($disabled_users)) {
            return Action::message(__('nova.2fa_disabled_for_users', ['users' => implode(', ', $disabled_users)]));
        } else {
            return Action::danger(__('nova.2fa_disabled_for_none'));
        }
    }

    /**
     * Get the fields available on the action.
     *
     * @return array
     */
    public function fields()
    {
        return [];
    }
}
