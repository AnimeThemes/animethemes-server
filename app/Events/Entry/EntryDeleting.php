<?php declare(strict_types=1);

namespace App\Events\Entry;

use App\Contracts\Events\UpdateRelatedIndicesEvent;
use App\Models\Video;

/**
 * Class EntryDeleting
 * @package App\Events\Entry
 */
class EntryDeleting extends EntryEvent implements UpdateRelatedIndicesEvent
{
    /**
     * Perform updates on related indices.
     *
     * @return void
     */
    public function updateRelatedIndices()
    {
        $entry = $this->getEntry();

        if ($entry->isForceDeleting()) {
            // refresh video documents by detaching entry
            $videos = $entry->videos;
            $entry->videos()->detach();
            $videos->each(function (Video $video) {
                $video->searchable();
            });
        }
    }
}
