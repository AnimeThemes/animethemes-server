<?php

declare(strict_types=1);

namespace App\Filament\Actions\Models\Auth\User;

use App\Events\Auth\Prohibition\ModelProhibited;
use App\Filament\Actions\BaseAction;
use App\Filament\Components\Fields\Select;
use App\Filament\Components\Fields\TextInput;
use App\Filament\Resources\Auth\Prohibition as ProhibitionResource;
use App\Models\Auth\Prohibition;
use App\Models\Auth\User;
use Filament\Actions\Action;
use Filament\Forms\Components\DateTimePicker;
use Filament\Schemas\Schema;
use Filament\Support\Colors\Color;
use Filament\Support\Icons\Heroicon;
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
        /** @var Prohibition $prohibition */
        $prohibition = Prohibition::query()->find(intval(Arr::get($data, self::FIELD_PROHIBITION)));

        $reason = Arr::get($data, 'reason');

        $expiresAt = ($expiresAtField = Arr::get($data, 'expires_at'))
            ? Date::createFromFormat('Y-m-d H:i:s', $expiresAtField)
            : null;

        $user->prohibit($prohibition, $expiresAt, $reason, Auth::user());

        event(new ModelProhibited($user, $prohibition, $expiresAt, $reason, Auth::user()));
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

                static::getExpiresAtField(),

                static::getReasonField(),
            ]);
    }

    public static function getExpiresAtField(): DateTimePicker
    {
        return DateTimePicker::make('expires_at')
            ->label(__('filament.actions.user.give_prohibition.expires_at.name'))
            ->helperText(__('filament.actions.user.give_prohibition.expires_at.help'))
            ->default(now())
            ->native(false)
            ->hintActions(static::getHintDateActions())
            ->suffixAction(
                Action::make('clear')
                    ->icon(Heroicon::Trash)
                    ->color(Color::Red)
                    ->action(fn (DateTimePicker $component): DateTimePicker => $component->state(null))
            )
            ->minDate(now())
            ->nullable();
    }

    public static function getReasonField(): TextInput
    {
        return TextInput::make('reason')
            ->label(__('filament.actions.user.give_prohibition.reason.name'))
            ->helperText(__('filament.actions.user.give_prohibition.reason.help'))
            ->required();
    }

    /**
     * Get hint actions for date picker.
     *
     * @return Action[]
     */
    public static function getHintDateActions(): array
    {
        return [
            Action::make('1 hour')
                ->label('+1 hour')
                ->action(
                    fn (DateTimePicker $component): DateTimePicker => $component->state(
                        Date::parse($component->getState() ?? now())
                            ->addHour()
                            ->format($component->getFormat())
                    )
                ),

            Action::make('1 day')
                ->label('+1 day')
                ->action(
                    fn (DateTimePicker $component): DateTimePicker => $component->state(
                        Date::parse($component->getState() ?? now())
                            ->addDay()
                            ->format($component->getFormat())
                    )
                ),

            Action::make('1 week')
                ->label('+1 week')
                ->action(
                    fn (DateTimePicker $component): DateTimePicker => $component->state(
                        Date::parse($component->getState() ?? now())
                            ->addWeek()
                            ->format($component->getFormat())
                    )
                ),

            Action::make('1 month')
                ->label('+1 month')
                ->action(
                    fn (DateTimePicker $component): DateTimePicker => $component->state(
                        Date::parse($component->getState() ?? now())
                            ->addMonth()
                            ->format($component->getFormat())
                    )
                ),
        ];
    }
}
