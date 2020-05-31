<?php

namespace App\Nova\Actions;

use App\Mail\RecoveryCodesMail;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Mail;
use Laravel\Nova\Actions\Action;
use Laravel\Nova\Fields\ActionFields;

class GenerateRecoveryCodesAction extends Action
{
    use InteractsWithQueue, Queueable;

    /**
     * Get the displayable name of the action.
     *
     * @return string
     */
    public function name()
    {
        return __('nova.recovery_codes_generate');
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
        $recovered_users = [];

        foreach ($models as $model) {
            // Don't send recovery codes for users that haven't enabled 2FA
            if ($model->hasTwoFactorEnabled()) {
                // Generate new recovery codes
                Mail::to($model->email)->queue(new RecoveryCodesMail($model));
                array_push($recovered_users, $model->name);
            }
        }

        if (!empty($recovered_users)) {
            return Action::message(__('nova.recovery_codes_for_users', ['users' => implode(', ', $recovered_users)]));
        } else {
            return Action::danger(__('nova.recovery_codes_for_none'));
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
