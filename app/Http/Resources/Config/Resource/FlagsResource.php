<?php

declare(strict_types=1);

namespace App\Http\Resources\Config\Resource;

use App\Constants\Config\FlagConstants;
use App\Http\Api\Query\Query;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Http\Resources\MissingValue;
use Illuminate\Support\Facades\Config;

/**
 * Class FlagsResource.
 */
class FlagsResource extends JsonResource
{
    /**
     * The "data" wrapper that should be applied.
     *
     * @var string|null
     */
    public static $wrap = 'flags';

    /**
     * Create a new resource instance.
     *
     * @param  Query  $query
     * @return void
     */
    public function __construct(protected readonly Query $query)
    {
        parent::__construct(new MissingValue());
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

        $criteria = $this->query->getFieldCriteria(static::$wrap);

        if ($criteria === null || $criteria->isAllowedField(FlagConstants::ALLOW_VIDEO_STREAMS_FLAG)) {
            $result[FlagConstants::ALLOW_VIDEO_STREAMS_FLAG] = Config::bool(FlagConstants::ALLOW_VIDEO_STREAMS_FLAG_QUALIFIED);
        }

        if ($criteria === null || $criteria->isAllowedField(FlagConstants::ALLOW_AUDIO_STREAMS_FLAG)) {
            $result[FlagConstants::ALLOW_AUDIO_STREAMS_FLAG] = Config::bool(FlagConstants::ALLOW_AUDIO_STREAMS_FLAG_QUALIFIED);
        }

        if ($criteria === null || $criteria->isAllowedField(FlagConstants::ALLOW_DISCORD_NOTIFICATIONS_FLAG)) {
            $result[FlagConstants::ALLOW_DISCORD_NOTIFICATIONS_FLAG] = Config::bool(FlagConstants::ALLOW_DISCORD_NOTIFICATIONS_FLAG_QUALIFIED);
        }

        if ($criteria === null || $criteria->isAllowedField(FlagConstants::ALLOW_VIEW_RECORDING_FLAG)) {
            $result[FlagConstants::ALLOW_VIEW_RECORDING_FLAG] = Config::bool(FlagConstants::ALLOW_VIEW_RECORDING_FLAG_QUALIFIED);
        }

        if ($criteria === null || $criteria->isAllowedField(FlagConstants::ALLOW_DUMP_DOWNLOADING_FLAG)) {
            $result[FlagConstants::ALLOW_DUMP_DOWNLOADING_FLAG] = Config::bool(FlagConstants::ALLOW_DUMP_DOWNLOADING_FLAG_QUALIFIED);
        }

        if ($criteria === null || $criteria->isAllowedField(FlagConstants::ALLOW_SCRIPT_DOWNLOADING_FLAG)) {
            $result[FlagConstants::ALLOW_SCRIPT_DOWNLOADING_FLAG] = Config::bool(FlagConstants::ALLOW_SCRIPT_DOWNLOADING_FLAG_QUALIFIED);
        }

        if ($criteria === null || $criteria->isAllowedField(FlagConstants::ALLOW_PLAYLIST_MANAGEMENT)) {
            $result[FlagConstants::ALLOW_PLAYLIST_MANAGEMENT] = Config::bool(FlagConstants::ALLOW_PLAYLIST_MANAGEMENT_QUALIFIED);
        }

        return $result;
    }
}
