<?php

declare(strict_types=1);

namespace App\Events\Admin\Dump;

use App\Events\Base\Admin\AdminUpdatedEvent;
use App\Models\Admin\Dump;

/**
 * @extends AdminUpdatedEvent<Dump>
 */
class DumpUpdated extends AdminUpdatedEvent
{
    public function __construct(Dump $dump)
    {
        parent::__construct($dump);
        $this->initializeEmbedFields($dump);
    }

    public function getModel(): Dump
    {
        return $this->model;
    }

    protected function getDiscordMessageDescription(): string
    {
        return "Dump '**{$this->getModel()->getName()}**' has been updated.";
    }
}
