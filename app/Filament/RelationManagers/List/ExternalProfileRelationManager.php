<?php

declare(strict_types=1);

namespace App\Filament\RelationManagers\List;

use App\Filament\RelationManagers\BaseRelationManager;
use App\Filament\Resources\BaseResource;
use App\Filament\Resources\List\ExternalProfile as ExternalProfileResource;
use App\Models\List\ExternalProfile;
use Filament\Tables\Table;

/**
 * Class ExternalProfileRelationManager.
 */
abstract class ExternalProfileRelationManager extends BaseRelationManager
{
    /**
     * The resource of the relation manager.
     *
     * @var class-string<BaseResource>|null
     */
    protected static ?string $relatedResource = ExternalProfileResource::class;

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
                ->recordTitleAttribute(ExternalProfile::ATTRIBUTE_NAME)
                ->defaultSort(ExternalProfile::TABLE.'.'.ExternalProfile::ATTRIBUTE_ID, 'desc')
        );
    }
}
