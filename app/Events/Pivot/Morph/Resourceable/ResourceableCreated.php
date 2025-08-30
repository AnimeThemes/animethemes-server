<?php

declare(strict_types=1);

namespace App\Events\Pivot\Morph\Resourceable;

use App\Concerns\Models\HasLabel;
use App\Contracts\Models\Nameable;
use App\Events\Base\Pivot\PivotCreatedEvent;
use App\Models\Wiki\ExternalResource;
use App\Pivots\Morph\Resourceable;
use Illuminate\Database\Eloquent\Model;

/**
 * @extends PivotCreatedEvent<Model&Nameable, ExternalResource>
 */
class ResourceableCreated extends PivotCreatedEvent
{
    use HasLabel {
        privateLabel as label;
    }

    public function __construct(Resourceable $resourceable)
    {
        parent::__construct($resourceable->resourceable, $resourceable->resource);
    }

    protected function getDiscordMessageDescription(): string
    {
        $foreign = $this->getForeign();
        $related = $this->getRelated();

        return "Resource '**{$foreign->getName()}**' has been attached to {$this->label($related)} '**{$related->getName()}**'.";
    }
}
