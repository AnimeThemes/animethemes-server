<?php

declare(strict_types=1);

namespace App\Filament\RelationManagers\Wiki\Anime;

use App\Filament\RelationManagers\BaseRelationManager;
use App\Filament\Resources\BaseResource;
use App\Filament\Resources\Wiki\Anime\Synonym as SynonymResource;
use App\Models\Wiki\Anime\AnimeSynonym;
use Filament\Tables\Table;

/**
 * Class SynonymRelationManager.
 */
abstract class SynonymRelationManager extends BaseRelationManager
{
    /**
     * The resource of the relation manager.
     *
     * @var class-string<BaseResource>|null
     */
    protected static ?string $relatedResource = SynonymResource::class;

    /**
     * The index page of the resource.
     *
     * @param  Table  $table
     * @return Table
     */
    public function table(Table $table): Table
    {
        return parent::table(
            $table
                ->recordTitleAttribute(AnimeSynonym::ATTRIBUTE_TEXT)
                ->defaultSort(AnimeSynonym::TABLE.'.'.AnimeSynonym::ATTRIBUTE_ID, 'desc')
        );
    }
}
