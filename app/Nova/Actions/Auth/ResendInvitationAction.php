<?php

declare(strict_types=1);

namespace App\Nova\Actions\Auth;

use App\Mail\InvitationMail;
use App\Models\Auth\Invitation;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Mail;
use Laravel\Nova\Actions\Action;
use Laravel\Nova\Fields\ActionFields;

/**
 * Class ResendInvitationAction.
 */
class ResendInvitationAction extends Action
{
    use InteractsWithQueue;
    use Queueable;

    /**
     * Get the displayable name of the action.
     *
     * @return string
     *
     * @noinspection PhpMissingParentCallCommonInspection
     */
    public function name(): string
    {
        return __('nova.resend_invitation');
    }

    /**
     * Perform the action on the given models.
     *
     * @param  ActionFields  $fields
     * @param  Collection  $models
     * @return array
     *
     * @noinspection PhpUnusedParameterInspection
     */
    public function handle(ActionFields $fields, Collection $models): array
    {
        $resentInvitations = [];

        $models->each(function (Invitation $invitation) use (&$resentInvitations) {
            // Don't send mail for invitation that have been claimed
            if ($invitation->isOpen()) {
                // Send replacement email
                Mail::to($invitation->email)->queue(new InvitationMail($invitation));
                array_push($resentInvitations, $invitation->name);
            }
        });

        if (! empty($resentInvitations)) {
            return Action::message(
                __('nova.resent_invitations_for_users', ['users' => implode(', ', $resentInvitations)])
            );
        } else {
            return Action::danger(__('nova.resent_invitations_for_none'));
        }
    }
}
