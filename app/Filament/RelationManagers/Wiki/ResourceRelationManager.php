<?php

declare(strict_types=1);

namespace App\Filament\RelationManagers\Wiki;

use App\Filament\Components\Columns\TextColumn;
use App\Filament\Components\Fields\TextInput;
use App\Filament\RelationManagers\BaseRelationManager;
use App\Filament\Resources\BaseResource;
use App\Filament\Resources\Wiki\ExternalResource as ExternalResourceResource;
use App\Models\Wiki\ExternalResource;
use App\Pivots\Morph\Resourceable;
use Filament\Schemas\Components\Component;
use Filament\Tables\Columns\Column;
use Filament\Tables\Table;

class ResourceRelationManager extends BaseRelationManager
{
    /**
     * The resource of the relation manager.
     *
     * @var class-string<BaseResource>|null
     */
    protected static ?string $relatedResource = ExternalResourceResource::class;

    /**
     * The relationship the relation manager corresponds to.
     */
    protected static string $relationship = 'resources';

    /**
     * @return Component[]
     */
    public function getPivotComponents(): array
    {
        return [
            TextInput::make(Resourceable::ATTRIBUTE_AS)
                ->label(__('filament.fields.resourceable.as.name'))
                ->helperText(__('filament.fields.resourceable.as.help')),
        ];
    }

    /**
     * @return Column[]
     */
    public function getPivotColumns(): array
    {
        return [
            TextColumn::make(Resourceable::ATTRIBUTE_AS)
                ->label(__('filament.fields.resourceable.as.name')),
        ];
    }

    public function table(Table $table): Table
    {
        return parent::table(
            $table
                ->recordTitleAttribute(ExternalResource::ATTRIBUTE_LINK)
                ->defaultSort(ExternalResource::TABLE.'.'.ExternalResource::ATTRIBUTE_ID, 'desc')
        );
    }
}
