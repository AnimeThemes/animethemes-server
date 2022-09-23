<?php

declare(strict_types=1);

namespace App\Http\Api\Field\Config\Flags;

use App\Constants\Config\FlagConstants;
use App\Http\Api\Field\Field;

/**
 * Class FlagsAllowScriptDownloadingField.
 */
class FlagsAllowScriptDownloadingField extends Field
{
    /**
     * Create a new field instance.
     */
    public function __construct()
    {
        parent::__construct(FlagConstants::ALLOW_SCRIPT_DOWNLOADING_FLAG);
    }
}
