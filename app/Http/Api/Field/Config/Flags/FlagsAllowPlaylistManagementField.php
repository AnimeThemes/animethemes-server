<?php

declare(strict_types=1);

namespace App\Http\Api\Field\Config\Flags;

use App\Constants\Config\FlagConstants;
use App\Http\Api\Field\Field;
use App\Http\Api\Schema\Schema;

/**
 * Class FlagsAllowPlaylistManagementField.
 */
class FlagsAllowPlaylistManagementField extends Field
{
    /**
     * Create a new field instance.
     *
     * @param  Schema  $schema
     */
    public function __construct(Schema $schema)
    {
        parent::__construct($schema, FlagConstants::ALLOW_PLAYLIST_MANAGEMENT);
    }
}
