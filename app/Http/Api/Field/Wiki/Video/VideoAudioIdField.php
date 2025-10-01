<?php

declare(strict_types=1);

namespace App\Http\Api\Field\Wiki\Video;

use App\Contracts\Http\Api\Field\CreatableField;
use App\Contracts\Http\Api\Field\SelectableField;
use App\Contracts\Http\Api\Field\UpdatableField;
use App\Http\Api\Field\Field;
use App\Http\Api\Query\Query;
use App\Http\Api\Schema\Schema;
use App\Models\Wiki\Audio;
use App\Models\Wiki\Video;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class VideoAudioIdField extends Field implements CreatableField, SelectableField, UpdatableField
{
    public function __construct(Schema $schema)
    {
        parent::__construct($schema, Video::ATTRIBUTE_AUDIO);
    }

    public function getCreationRules(Request $request): array
    {
        return [
            'sometimes',
            'required',
            'integer',
            Rule::exists(Audio::class, Audio::ATTRIBUTE_ID),
        ];
    }

    public function shouldSelect(Query $query, Schema $schema): bool
    {
        // Needed to match audio relation.
        return true;
    }

    public function getUpdateRules(Request $request): array
    {
        return [
            'sometimes',
            'required',
            'integer',
            Rule::exists(Audio::class, Audio::ATTRIBUTE_ID),
        ];
    }
}
