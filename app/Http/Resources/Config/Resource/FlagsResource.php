<?php

declare(strict_types=1);

namespace App\Http\Resources\Config\Resource;

use App\Constants\Config\FlagConstants;
use App\Http\Api\Query\ReadQuery;
use App\Http\Api\Schema\Config\FlagsSchema;
use App\Http\Api\Schema\Schema;
use App\Http\Resources\BaseResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\MissingValue;
use Illuminate\Support\Facades\Config;

/**
 * Class FlagsResource.
 */
class FlagsResource extends BaseResource
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
     * @param  ReadQuery  $query
     * @return void
     */
    public function __construct(ReadQuery $query)
    {
        parent::__construct(new MissingValue(), $query);
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

        if ($this->isAllowedField(FlagConstants::ALLOW_VIDEO_STREAMS_FLAG)) {
            $result[FlagConstants::ALLOW_VIDEO_STREAMS_FLAG] = Config::bool(FlagConstants::ALLOW_VIDEO_STREAMS_FLAG_QUALIFIED);
        }

        if ($this->isAllowedField(FlagConstants::ALLOW_AUDIO_STREAMS_FLAG)) {
            $result[FlagConstants::ALLOW_AUDIO_STREAMS_FLAG] = Config::bool(FlagConstants::ALLOW_AUDIO_STREAMS_FLAG_QUALIFIED);
        }

        if ($this->isAllowedField(FlagConstants::ALLOW_DISCORD_NOTIFICATIONS_FLAG)) {
            $result[FlagConstants::ALLOW_DISCORD_NOTIFICATIONS_FLAG] = Config::bool(FlagConstants::ALLOW_DISCORD_NOTIFICATIONS_FLAG_QUALIFIED);
        }

        if ($this->isAllowedField(FlagConstants::ALLOW_VIEW_RECORDING_FLAG)) {
            $result[FlagConstants::ALLOW_VIEW_RECORDING_FLAG] = Config::bool(FlagConstants::ALLOW_VIEW_RECORDING_FLAG_QUALIFIED);
        }

        if ($this->isAllowedField(FlagConstants::ALLOW_DUMP_DOWNLOADING_FLAG)) {
            $result[FlagConstants::ALLOW_DUMP_DOWNLOADING_FLAG] = Config::bool(FlagConstants::ALLOW_DUMP_DOWNLOADING_FLAG_QUALIFIED);
        }

        if ($this->isAllowedField(FlagConstants::ALLOW_SCRIPT_DOWNLOADING_FLAG)) {
            $result[FlagConstants::ALLOW_SCRIPT_DOWNLOADING_FLAG] = Config::bool(FlagConstants::ALLOW_SCRIPT_DOWNLOADING_FLAG_QUALIFIED);
        }

        return $result;
    }

    /**
     * Get the resource schema.
     *
     * @return Schema
     */
    protected function schema(): Schema
    {
        return new FlagsSchema();
    }
}
