<?php

declare(strict_types=1);

namespace App\Concerns\Enums;

use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Str;

trait LocalizesName
{
    /**
     * Localize the enum.
     */
    public function localize(?string $locale = null): ?string
    {
        return $this->getLocalizedName($locale)
            ?? $this->getPrettyName();
    }

    /**
     * Alias of localize() for backward compatibility.
     */
    public function getLabel(): ?string
    {
        return $this->localize();
    }

    /**
     * Get the localized name for the derived translation key.
     */
    protected function getLocalizedName(?string $locale = null): ?string
    {
        $localizationKey = $this->getLocalizationKey();

        if (Lang::has($localizationKey, $locale)) {
            return __($localizationKey);
        }

        return null;
    }

    /**
     * Get a pretty-printed version of the enum value.
     */
    protected function getPrettyName(): string
    {
        return Str::of($this->name)
            ->lower()
            ->headline()
            ->__toString();
    }

    /**
     * Get the default localization key.
     */
    protected function getLocalizationKey(): string
    {
        return Str::of('enums.')
            ->append(get_class($this))
            ->append('.')
            ->append($this->name)
            ->__toString();
    }

    /**
     * Get the enum as an array formatted for a select.
     *
     * @return array
     */
    public static function asSelectArray(?string $locale = null): array
    {
        $selectArray = [];

        /** @var static $case */
        foreach (static::cases() as $case) {
            $selectArray[$case->value] = $case->localize($locale);
        }

        return $selectArray;
    }

    /**
     * Make a new instance from the localized name.
     */
    public static function fromLocalizedName(string $localizedName, ?string $locale = null): ?static
    {
        return Arr::first(
            static::cases(),
            fn (self $enum) => Str::lower($enum->localize($locale)) === Str::lower($localizedName)
        );
    }
}
