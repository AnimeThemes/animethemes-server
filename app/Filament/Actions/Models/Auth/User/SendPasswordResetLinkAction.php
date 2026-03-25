<?php

declare(strict_types=1);

namespace App\Filament\Actions\Models\Auth\User;

use App\Filament\Actions\BaseAction;
use App\Models\Auth\User;
use Filament\Support\Icons\Heroicon;
use Illuminate\Support\Facades\Password;

class SendPasswordResetLinkAction extends BaseAction
{
    final public const string FIELD_PERMISSION = 'permission';

    public static function getDefaultName(): ?string
    {
        return 'send-password-link-reset';
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->label(__('filament.actions.user.send_password_reset_link'));

        $this->icon(Heroicon::Envelope);

        $this->action(fn (User $record) => $this->handle($record));
    }

    /**
     * Perform the action on the given model.
     */
    public function handle(User $user): void
    {
        Password::sendResetLink([User::ATTRIBUTE_EMAIL => $user->email]);
    }
}
