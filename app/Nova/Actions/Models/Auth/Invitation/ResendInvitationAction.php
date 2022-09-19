<?php

declare(strict_types=1);

namespace App\Nova\Actions\Models\Auth\Invitation;

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
        return __('nova.actions.invitation.resend.name');
    }

    /**
     * Perform the action on the given models.
     *
     * @param  ActionFields  $fields
     * @param  Collection<int, Invitation>  $models
     * @return array
     *
     * @noinspection PhpUnusedParameterInspection
     */
    public function handle(ActionFields $fields, Collection $models): array
    {
        $resentInvitations = [];

        foreach ($models as $invitation) {
            // Don't send mail for invitation that have been claimed
            if ($invitation->isOpen()) {
                // Send replacement email
                Mail::to($invitation->email)->queue(new InvitationMail($invitation));
                $resentInvitations[] = $invitation->name;
            }
        }

        if (! empty($resentInvitations)) {
            return Action::message(
                __('nova.actions.invitation.resend.message.success', ['users' => implode(', ', $resentInvitations)])
            );
        }

        return Action::danger(__('nova.actions.invitation.resend.message.failure'));
    }
}
