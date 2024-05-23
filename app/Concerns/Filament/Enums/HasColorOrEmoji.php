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
            $selectArray[$case->value] = $case->localizeStyled($hasColor, $hasEmoji, $locale);
        }

        return $selectArray;
    }

    /**
     * Localize the enum, but styled.
     *
     * @param  bool|null  $hasColor
     * @param  bool|null  $hasEmoji
     * @param  string|null  $locale
     * @return string
     */
    public function localizeStyled(?bool $hasColor = true, ?bool $hasEmoji = true, ?string $locale = null): string
    {
        $localizedName = $this->getLocalizedName($locale) ?? $this->getPrettyName();

        $name = Str::of('');

        if ($hasColor) {
            $color = $this->getColor();
            $name = $name->append("<p style='color: rgb($color);'>");
        }

        if ($hasEmoji) {
            $emoji = $this->getEmoji();
            $name = $name->append("$emoji ");
        }

        $name = $name->append($localizedName);

        if ($hasColor) {
            $name = $name->append('</p>');
        }

        return $name->__toString();
    }
}