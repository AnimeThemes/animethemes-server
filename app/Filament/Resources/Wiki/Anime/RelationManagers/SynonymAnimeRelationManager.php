<?php

declare(strict_types=1);

namespace App\Filament\Resources\Wiki\Anime\RelationManagers;

use App\Filament\RelationManagers\Wiki\Anime\SynonymRelationManager;
use App\Models\Wiki\Anime;
use App\Models\Wiki\Anime\AnimeSynonym as SynonymModel;
use Filament\Tables\Table;

/**
 * Class SynonymAnimeRelationManager.
 */
class SynonymAnimeRelationManager extends SynonymRelationManager
{
    /**
     * The relationship the relation manager corresponds to.
     *
     * @var string
     */
    protected static string $relationship = Anime::RELATION_SYNONYMS;

    /**
     * The index page of the Synonym.
     *
     * @param  Table  $table
     * @return Table
     *
     * @noinspection PhpMissingParentCallCommonInspection
     */
    public function table(Table $table): Table
    {
        return parent::table(
            $table
                ->inverseRelationship(SynonymModel::RELATION_ANIME)
        );
    }
}
