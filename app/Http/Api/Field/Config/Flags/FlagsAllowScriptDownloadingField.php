<?php

declare(strict_types=1);

namespace App\Http\Api\Field\Config\Flags;

use App\Constants\Config\FlagConstants;
use App\Http\Api\Field\Field;
use App\Http\Api\Schema\Schema;

/**
 * Class FlagsAllowScriptDownloadingField.
 */
class FlagsAllowScriptDownloadingField extends Field
{
    /**
     * Create a new field instance.
	 *
	 * @param  Schema  $schema
     */
    public function __construct(Schema $schema)
    {
        parent::__construct($schema, FlagConstants::ALLOW_SCRIPT_DOWNLOADING_FLAG);
    }
}
