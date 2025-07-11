<?php

declare(strict_types=1);

namespace App\Filament\RelationManagers\Wiki;

use App\Enums\Auth\Role;
use App\Filament\RelationManagers\BaseRelationManager;
use App\Filament\Resources\BaseResource;
use App\Filament\Resources\Wiki\ExternalResource as ExternalResourceResource;
use App\Models\Wiki\ExternalResource;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Auth;

/**
 * Class ResourceRelationManager.
 */
abstract class ResourceRelationManager extends BaseRelationManager
{
    /**
     * The resource of the relation manager.
     *
     * @var class-string<BaseResource>|null
     */
    protected static ?string $relatedResource = ExternalResourceResource::class;

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
                ->recordTitleAttribute(ExternalResource::ATTRIBUTE_LINK)
                ->defaultSort(ExternalResource::TABLE.'.'.ExternalResource::ATTRIBUTE_ID, 'desc')
        );
    }

    /**
     * Determine whether the related model can be created.
     *
     * @return bool
     */
    public function canCreate(): bool
    {
        return Auth::user()->hasRole(Role::ADMIN->value);
    }
}
