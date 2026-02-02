<?php

declare(strict_types=1);

namespace App\Filament\Actions\Models\Auth\Sanction;

use App\Filament\Actions\BaseAction;
use App\Filament\Components\Fields\Select;
use App\Filament\Resources\Auth\ProhibitionResource;
use App\Models\Auth\Prohibition;
use App\Models\Auth\Sanction;
use Filament\Schemas\Schema;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Arr;

class RevokeProhibitionAction extends BaseAction
{
    final public const string FIELD_PROHIBITIONS = 'prohibitions';

    public static function getDefaultName(): ?string
    {
        return 'sanction-revoke-prohibition';
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->label(__('filament.actions.sanction.revoke_prohibition.name'));

        $this->icon(ProhibitionResource::getNavigationIcon());

        $this->action(fn (Sanction $record, array $data) => $this->handle($record, $data));
    }

    /**
     * Perform the action on the given model.
     *
     * @param  array<string, mixed>  $data
     */
    public function handle(Sanction $sanction, array $data): void
    {
        $prohibitions = Arr::get($data, self::FIELD_PROHIBITIONS);

        $sanction->prohibitions()->detach($prohibitions);
    }

    public function getSchema(Schema $schema): Schema
    {
        $prohibitions = Prohibition::query()
            ->whereHas(Prohibition::RELATION_SANCTIONS, fn (Builder $query) => $query->whereKey($this->getRecord()->getKey()))
            ->get([Prohibition::ATTRIBUTE_ID, Prohibition::ATTRIBUTE_NAME])
            ->keyBy(Prohibition::ATTRIBUTE_ID)
            ->map(fn (Prohibition $prohibition) => $prohibition->name)
            ->toArray();

        return $schema
            ->components([
                Select::make(self::FIELD_PROHIBITIONS)
                    ->label(__('filament.resources.label.prohibitions'))
                    ->searchable()
                    ->multiple()
                    ->required()
                    ->options($prohibitions),
            ]);
    }
}
