<?php

declare(strict_types=1);

namespace App\Concerns\Models;

use App\Contracts\Models\HasSynonyms;
use App\Contracts\Models\Nameable;
use App\Enums\Models\Wiki\SynonymType;
use App\Models\Wiki\Synonym;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

trait CanCreateSynonym
{
    use HasLabel;

    public function createSynonym(?string $text, int $type, Model&Nameable&HasSynonyms $synonymable): void
    {
        if (
            blank($text)
            || ($type === SynonymType::OTHER->value && Str::is($text, $synonymable->getName(), true))
        ) {
            return;
        }

        Log::info("Creating {$text} for {$this->privateLabel($synonymable)} {$synonymable->getName()}");

        $synonymable->synonyms()->create([
            Synonym::ATTRIBUTE_TEXT => $text,
            Synonym::ATTRIBUTE_TYPE => $type,
        ]);
    }
}
