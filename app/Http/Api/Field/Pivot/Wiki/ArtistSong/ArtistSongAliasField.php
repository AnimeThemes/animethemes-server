<?php

declare(strict_types=1);

namespace App\Http\Api\Field\Pivot\Wiki\ArtistSong;

use App\Contracts\Http\Api\Field\CreatableField;
use App\Contracts\Http\Api\Field\UpdatableField;
use App\Http\Api\Field\StringField;
use App\Http\Api\Schema\Schema;
use App\Pivots\Wiki\ArtistSong;
use Illuminate\Http\Request;

class ArtistSongAliasField extends StringField implements CreatableField, UpdatableField
{
    public function __construct(Schema $schema)
    {
        parent::__construct($schema, ArtistSong::ATTRIBUTE_ALIAS);
    }

    /**
     * @return array
     */
    public function getCreationRules(Request $request): array
    {
        return [
            'nullable',
            'string',
            'max:192',
        ];
    }

    /**
     * @return array
     */
    public function getUpdateRules(Request $request): array
    {
        return [
            'nullable',
            'string',
            'max:192',
        ];
    }
}
