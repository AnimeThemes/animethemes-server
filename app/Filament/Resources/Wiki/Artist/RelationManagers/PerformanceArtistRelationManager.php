<?php

declare(strict_types=1);

namespace App\Filament\Resources\Wiki\Artist\RelationManagers;

use App\Filament\Actions\Base\CreateAction;
use App\Filament\Components\Fields\BelongsTo;
use App\Filament\Components\Fields\TextInput;
use App\Filament\RelationManagers\Wiki\Song\PerformanceRelationManager;
use App\Filament\Resources\Wiki\ArtistResource;
use App\Models\Wiki\Artist;
use App\Models\Wiki\Song\Performance;
use Filament\Actions\Action;
use Filament\Schemas\Components\Component;

class PerformanceArtistRelationManager extends PerformanceRelationManager
{
    /**
     * The relationship the relation manager corresponds to.
     */
    protected static string $relationship = Artist::RELATION_PERFORMANCES;

    /**
     * @return Component[]
     */
    public function getPivotComponents(): array
    {
        return [
            TextInput::make(Performance::ATTRIBUTE_AS)
                ->label(__('filament.fields.performance.as.name'))
                ->helperText(__('filament.fields.performance.as.help')),

            TextInput::make(Performance::ATTRIBUTE_ALIAS)
                ->label(__('filament.fields.performance.alias.name'))
                ->helperText(__('filament.fields.performance.alias.help')),

            BelongsTo::make(Performance::ATTRIBUTE_MEMBER)
                ->resource(ArtistResource::class)
                ->label(__('filament.fields.performance.member'))
                ->columnSpanFull(),

            TextInput::make(Performance::ATTRIBUTE_MEMBER_AS)
                ->label(__('filament.fields.performance.member_as.name'))
                ->helperText(__('filament.fields.performance.member_as.help')),

            TextInput::make(Performance::ATTRIBUTE_MEMBER_ALIAS)
                ->label(__('filament.fields.performance.member_alias.name'))
                ->helperText(__('filament.fields.performance.member_alias.help')),
        ];
    }

    /**
     * Get the header actions available for the relation.
     *
     * @return array<int, Action>
     */
    public static function getHeaderActions(): array
    {
        return [
            CreateAction::make('new-performance')
                ->after(fn (Performance $record): Performance => $record->moveToEnd()),
        ];
    }
}
