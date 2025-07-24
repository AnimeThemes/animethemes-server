<?php

declare(strict_types=1);

namespace App\Events\Admin\Dump;

use App\Events\Base\Admin\AdminUpdatedEvent;
use App\Models\Admin\Dump;

/**
 * Class DumpUpdated.
 *
 * @extends AdminUpdatedEvent<Dump>
 */
class DumpUpdated extends AdminUpdatedEvent
{
    public function __construct(Dump $dump)
    {
        parent::__construct($dump);
        $this->initializeEmbedFields($dump);
    }

    /**
     * Get the model that has fired this event.
     */
    public function getModel(): Dump
    {
        return $this->model;
    }

    /**
     * Get the description for the Discord message payload.
     */
    protected function getDiscordMessageDescription(): string
    {
        return "Dump '**{$this->getModel()->getName()}**' has been updated.";
    }
}
