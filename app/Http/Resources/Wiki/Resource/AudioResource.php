<?php

declare(strict_types=1);

namespace App\Http\Resources\Wiki\Resource;

use App\Http\Api\Query\ReadQuery;
use App\Http\Resources\BaseResource;
use App\Models\BaseModel;
use App\Models\Wiki\Audio;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\MissingValue;

/**
 * Class AudioResource.
 *
 * @mixin Audio
 */
class AudioResource extends BaseResource
{
    final public const ATTRIBUTE_LINK = 'link';

    /**
     * The "data" wrapper that should be applied.
     *
     * @var string|null
     */
    public static $wrap = 'audio';

    /**
     * Create a new resource instance.
     *
     * @param  Audio | MissingValue | null  $audio
     * @param  ReadQuery  $query
     * @return void
     */
    public function __construct(Audio|MissingValue|null $audio, ReadQuery $query)
    {
        parent::__construct($audio, $query);
    }

    /**
     * Transform the resource into an array.
     *
     * @param  Request  $request
     * @return array
     *
     * @noinspection PhpMissingParentCallCommonInspection
     */
    public function toArray($request): array
    {
        $result = [];

        if ($this->isAllowedField(BaseResource::ATTRIBUTE_ID)) {
            $result[BaseResource::ATTRIBUTE_ID] = $this->getKey();
        }

        if ($this->isAllowedField(Audio::ATTRIBUTE_BASENAME)) {
            $result[Audio::ATTRIBUTE_BASENAME] = $this->basename;
        }

        if ($this->isAllowedField(Audio::ATTRIBUTE_FILENAME)) {
            $result[Audio::ATTRIBUTE_FILENAME] = $this->filename;
        }

        if ($this->isAllowedField(Audio::ATTRIBUTE_PATH)) {
            $result[Audio::ATTRIBUTE_PATH] = $this->path;
        }

        if ($this->isAllowedField(Audio::ATTRIBUTE_SIZE)) {
            $result[Audio::ATTRIBUTE_SIZE] = $this->size;
        }

        if ($this->isAllowedField(Audio::ATTRIBUTE_MIMETYPE)) {
            $result[Audio::ATTRIBUTE_MIMETYPE] = $this->mimetype;
        }

        if ($this->isAllowedField(BaseModel::ATTRIBUTE_CREATED_AT)) {
            $result[BaseModel::ATTRIBUTE_CREATED_AT] = $this->created_at;
        }

        if ($this->isAllowedField(BaseModel::ATTRIBUTE_UPDATED_AT)) {
            $result[BaseModel::ATTRIBUTE_UPDATED_AT] = $this->updated_at;
        }

        if ($this->isAllowedField(BaseModel::ATTRIBUTE_DELETED_AT)) {
            $result[BaseModel::ATTRIBUTE_DELETED_AT] = $this->deleted_at;
        }

        if ($this->isAllowedField(AudioResource::ATTRIBUTE_LINK)) {
            $result[AudioResource::ATTRIBUTE_LINK] = route('audio.show', $this);
        }

        return $result;
    }
}
