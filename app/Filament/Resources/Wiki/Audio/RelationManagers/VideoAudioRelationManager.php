<?php

declare(strict_types=1);

namespace App\Filament\Resources\Wiki\Audio\RelationManagers;

use App\Filament\RelationManagers\Wiki\VideoRelationManager;
use App\Models\Wiki\Audio;
use App\Models\Wiki\Video;
use Filament\Tables\Table;

/**
 * Class VideoAudioRelationManager.
 */
class VideoAudioRelationManager extends VideoRelationManager
{
    /**
     * The relationship the relation manager corresponds to.
     *
     * @var string
     */
    protected static string $relationship = Audio::RELATION_VIDEOS;

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
                ->inverseRelationship(Video::RELATION_AUDIO)
        );
    }
}
