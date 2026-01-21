<?php

declare(strict_types=1);

namespace App\Filament\Actions\Models\Auth\User;

use App\Filament\Actions\BaseAction;
use App\Filament\Components\Fields\Select;
use App\Filament\Components\Fields\TextInput;
use App\Filament\Resources\Auth\Prohibition as ProhibitionResource;
use App\Models\Auth\Prohibition;
use App\Models\Auth\User;
use Filament\Forms\Components\DateTimePicker;
use Filament\Schemas\Schema;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Date;

class GiveProhibitionAction extends BaseAction
{
    final public const string FIELD_PROHIBITION = 'prohibition';

    public static function getDefaultName(): ?string
    {
        return 'user-give-prohibition';
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->label(__('filament.actions.user.give_prohibition.name'));

        $this->icon(ProhibitionResource::getNavigationIcon());

        $this->action(fn (User $record, array $data) => $this->handle($record, $data));
    }

    /**
     * Perform the action on the given model.
     *
     * @param  array<string, mixed>  $data
     */
    public function handle(User $user, array $data): void
    {
        $prohibition = Prohibition::query()->find(intval(Arr::get($data, self::FIELD_PROHIBITION)));

        $reason = Arr::get($data, 'reason');

        $expiresAt = Date::createFromFormat('Y-m-d H:i:s', Arr::get($data, 'expires_at'));

        $user->prohibit($prohibition, $expiresAt, $reason, Auth::user());
    }

    public function getSchema(Schema $schema): Schema
    {
        $prohibitions = Prohibition::query()
            ->get([Prohibition::ATTRIBUTE_ID, Prohibition::ATTRIBUTE_NAME])
            ->keyBy(Prohibition::ATTRIBUTE_ID)
            ->map(fn (Prohibition $prohibition) => $prohibition->name)
            ->toArray();

        return $schema
            ->components([
                Select::make(self::FIELD_PROHIBITION)
                    ->label(__('filament.resources.singularLabel.prohibition'))
                    ->searchable()
                    ->required()
                    ->options($prohibitions),

                DateTimePicker::make('expires_at')
                    ->label(__('filament.actions.user.give_prohibition.expires_at.name'))
                    ->helperText(__('filament.actions.user.give_prohibition.expires_at.help'))
                    ->nullable(),

                TextInput::make('reason')
                    ->label(__('filament.actions.user.give_prohibition.reason.name'))
                    ->helperText(__('filament.actions.user.give_prohibition.reason.help'))
                    ->required(),
            ]);
    }
}
