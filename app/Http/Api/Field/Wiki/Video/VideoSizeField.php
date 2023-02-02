<?php

declare(strict_types=1);

namespace App\Http\Api\Field\Wiki\Video;

use App\Contracts\Http\Api\Field\CreatableField;
use App\Http\Api\Field\IntField;
use App\Http\Api\Schema\Schema;
use App\Models\Wiki\Video;
use Illuminate\Http\Request;

/**
 * Class VideoSizeField.
 */
class VideoSizeField extends IntField implements CreatableField
{
    /**
     * Create a new field instance.
	 *
	 * @param  Schema  $schema
     */
    public function __construct(Schema $schema)
    {
        parent::__construct($schema, Video::ATTRIBUTE_SIZE);
    }

    /**
     * Set the creation validation rules for the field.
     *
     * @param  Request  $request
     * @return array
     */
    public function getCreationRules(Request $request): array
    {
        return [
            'required',
            'integer',
            'min:0',
        ];
    }
}
