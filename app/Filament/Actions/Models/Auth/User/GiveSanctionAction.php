<?php

declare(strict_types=1);

namespace App\Filament\Actions\Models\Auth\User;

use App\Events\Auth\Sanction\ModelSanctioned;
use App\Filament\Actions\BaseAction;
use App\Filament\Components\Fields\Select;
use App\Filament\Resources\Auth\Sanction as SanctionResource;
use App\Models\Auth\Sanction;
use App\Models\Auth\User;
use Filament\Schemas\Schema;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Date;

class GiveSanctionAction extends BaseAction
{
    final public const string FIELD_SANCTION = 'sanction';

    public static function getDefaultName(): ?string
    {
        return 'user-give-sanction';
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->label(__('filament.actions.user.give_sanction.name'));

        $this->icon(SanctionResource::getNavigationIcon());

        $this->action(fn (User $record, array $data) => $this->handle($record, $data));
    }

    /**
     * Perform the action on the given model.
     *
     * @param  array<string, mixed>  $data
     */
    public function handle(User $user, array $data): void
    {
        $sanction = Sanction::query()->find(intval(Arr::get($data, self::FIELD_SANCTION)));

        $reason = Arr::get($data, 'reason');

        $expiresAt = ($expiresAtField = Arr::get($data, 'expires_at'))
            ? Date::createFromFormat('Y-m-d H:i:s', $expiresAtField)
            : null;

        $user->applySanction($sanction, $expiresAt, $reason, Auth::user());

        event(new ModelSanctioned($user, $sanction, $expiresAt, $reason, Auth::user()));
    }

    public function getSchema(Schema $schema): Schema
    {
        $sanctions = Sanction::query()
            ->get([Sanction::ATTRIBUTE_ID, Sanction::ATTRIBUTE_NAME])
            ->keyBy(Sanction::ATTRIBUTE_ID)
            ->map(fn (Sanction $sanction) => $sanction->name)
            ->toArray();

        return $schema
            ->components([
                Select::make(self::FIELD_SANCTION)
                    ->label(__('filament.resources.singularLabel.sanction'))
                    ->searchable()
                    ->required()
                    ->options($sanctions),

                GiveProhibitionAction::getExpiresAtField()
                    ->helperText(__('filament.actions.user.give_sanction.expires_at.help')),

                GiveProhibitionAction::getReasonField()
                    ->helperText(__('filament.actions.user.give_sanction.reason.help')),
            ]);
    }
}
