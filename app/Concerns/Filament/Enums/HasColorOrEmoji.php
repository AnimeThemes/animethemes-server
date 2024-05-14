<?php

declare(strict_types=1);

namespace App\Concerns\Filament\Enums;

use Illuminate\Support\Str;

/**
 * Trait HasColorOrEmoji.
 */
trait HasColorOrEmoji
{
    /**
     * Get the enum as an array formatted for a select, but styled.
     *
     * @param  bool|null  $hasColor
     * @param  bool|null  $hasEmoji
     * @param  string|null  $locale
     * @return array
     */
    public static function asSelectArrayStyled(?bool $hasColor = true, ?bool $hasEmoji = true, ?string $locale = null): array
    {
        $selectArray = [];

        /** @var static $case */
        foreach (static::cases() as $case) {
            $name = Str::of('');

            if ($hasColor) {
                $color = $case->getColor();
                $name = $name->append("<p style='color: rgb($color);'>");
            }

            if ($hasEmoji) {
                $emoji = $case->getEmoji();
                $name = $name->append("$emoji ");
            }

            $name = $name->append($case->localize($locale));

            if ($hasColor) {
                $name = $name->append('</p>');
            }

            $selectArray[$case->value] = $name->__toString();
        }

        return $selectArray;
    }
}