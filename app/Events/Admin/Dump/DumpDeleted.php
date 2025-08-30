<?php

declare(strict_types=1);

namespace App\Events\Admin\Dump;

use App\Events\Base\Admin\AdminDeletedEvent;
use App\Models\Admin\Dump;

/**
 * @extends AdminDeletedEvent<Dump>
 */
class DumpDeleted extends AdminDeletedEvent
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
        return "Dump '**{$this->getModel()->getName()}**' has been deleted.";
    }
}
