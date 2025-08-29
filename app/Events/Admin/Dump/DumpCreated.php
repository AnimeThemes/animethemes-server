<?php

declare(strict_types=1);

namespace App\Events\Admin\Dump;

use App\Events\Base\Admin\AdminCreatedEvent;
use App\Models\Admin\Dump;

/**
 * @extends AdminCreatedEvent<Dump>
 */
class DumpCreated extends AdminCreatedEvent
{
    public function __construct(Dump $dump)
    {
        parent::__construct($dump);
    }

    public function getModel(): Dump
    {
        return $this->model;
    }

    protected function getDiscordMessageDescription(): string
    {
        return "Dump '**{$this->getModel()->getName()}**' has been created.";
    }
}
