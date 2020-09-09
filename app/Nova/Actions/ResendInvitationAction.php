<?php

namespace App\Nova\Actions;

use App\Mail\InvitationEmail;
use App\Models\Invitation;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Mail;
use Laravel\Nova\Actions\Action;
use Laravel\Nova\Fields\ActionFields;

class ResendInvitationAction extends Action
{
    use InteractsWithQueue, Queueable;

    /**
     * Get the displayable name of the action.
     *
     * @return string
     */
    public function name()
    {
        return __('nova.resend_invitation');
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
        $resent_invitations = [];

        foreach ($models as $model) {
            // Don't send mail for invitation that have been claimed
            if ($model->isOpen()) {
                // Reset token
                $model->token = Invitation::createToken();
                $model->save();

                // Send replacement email
                Mail::to($model->email)->queue(new InvitationEmail($model));
                array_push($resent_invitations, $model->name);
            }
        }

        if (!empty($resent_invitations)) {
            return Action::message(__('nova.resent_invitations_for_users', ['users' => implode(', ', $resent_invitations)]));
        } else {
            return Action::danger(__('nova.resent_invitations_for_none'));
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
