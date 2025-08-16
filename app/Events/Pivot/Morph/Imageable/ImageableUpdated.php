<?php

declare(strict_types=1);

namespace App\Events\Pivot\Morph\Imageable;

use App\Concerns\Models\HasLabel;
use App\Constants\Config\ServiceConstants;
use App\Contracts\Models\Nameable;
use App\Events\Base\Pivot\PivotUpdatedEvent;
use App\Models\List\Playlist;
use App\Models\Wiki\Image;
use App\Pivots\Morph\Imageable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Config;

/**
 * Class ImageableUpdated.
 *
 * @extends PivotUpdatedEvent<Model&Nameable, Image>
 */
class ImageableUpdated extends PivotUpdatedEvent
{
    use HasLabel {
        privateLabel as label;
    }

    public function __construct(Imageable $imageable)
    {
        parent::__construct($imageable->imageable, $imageable->image);
        $this->initializeEmbedFields($imageable);
    }

    /**
     * Get Discord channel the message will be sent to.
     *
     * @noinspection PhpMissingParentCallCommonInspection
     */
    public function getDiscordChannel(): string
    {
        return $this->getRelated() instanceof Playlist
            ? Config::get(ServiceConstants::ADMIN_DISCORD_CHANNEL_QUALIFIED)
            : parent::getDiscordChannel();
    }

    /**
     * Get the description for the Discord message payload.
     */
    protected function getDiscordMessageDescription(): string
    {
        $foreign = $this->getForeign();
        $related = $this->getRelated();

        return "Image '**{$foreign->getName()}**' for {$this->label($related)} '**{$related->getName()}**' has been updated.";
    }
}
