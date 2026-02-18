<?php

declare(strict_types=1);

namespace App\Contracts\Models;

use App\Models\Wiki\Synonym;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphMany;

interface HasSynonyms
{
    public const SYNONYMS_RELATION = 'synonyms';

    /**
     * Get the synonyms for the owner model.
     *
     * @return MorphMany<Synonym, Model&HasSynonyms>
     */
    public function synonyms(): MorphMany;
}
