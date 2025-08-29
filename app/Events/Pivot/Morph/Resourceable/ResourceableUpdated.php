<?php

declare(strict_types=1);

namespace App\Events\Pivot\Morph\Resourceable;

use App\Concerns\Models\HasLabel;
use App\Contracts\Models\Nameable;
use App\Events\Base\Pivot\PivotUpdatedEvent;
use App\Models\Wiki\ExternalResource;
use App\Pivots\Morph\Resourceable;
use Illuminate\Database\Eloquent\Model;

/**
 * @extends PivotUpdatedEvent<Model&Nameable, ExternalResource>
 */
class ResourceableUpdated extends PivotUpdatedEvent
{
    use HasLabel {
        privateLabel as label;
    }

    public function __construct(Resourceable $resourceable)
    {
        parent::__construct($resourceable->resourceable, $resourceable->resource);
        $this->initializeEmbedFields($resourceable);
    }

    /**
     * Get the description for the Discord message payload.
     */
    protected function getDiscordMessageDescription(): string
    {
        $foreign = $this->getForeign();
        $related = $this->getRelated();

        return "Resource '**{$foreign->getName()}**' for {$this->label($related)} '**{$related->getName()}**' has been updated.";
    }
}
