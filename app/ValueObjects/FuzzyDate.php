<?php

declare(strict_types=1);

namespace App\ValueObjects;

use Illuminate\Contracts\Support\Arrayable;
use InvalidArgumentException;
use Stringable;

class FuzzyDate implements Arrayable, Stringable
{
    public function __construct(
        public ?int $year = null,
        public ?int $month = null,
        public ?int $day = null,
    ) {
        $this->validate();
    }

    public static function fromString(?string $value): ?FuzzyDate
    {
        if (blank($value) || $value === '00000000') {
            return null;
        }

        throw_unless(preg_match('/^\d{8}$/', $value), InvalidArgumentException::class, 'FuzzyDate must be an 8-digit string.');

        return new FuzzyDate(
            (int) substr($value, 0, 4) ?: null,
            (int) substr($value, 4, 2) ?: null,
            (int) substr($value, 6, 2) ?: null,
        );
    }

    public function __toString(): string
    {
        return str_pad((string) ($this->year ?? 0), 4, '0', STR_PAD_LEFT)
            .str_pad((string) ($this->month ?? 0), 2, '0', STR_PAD_LEFT)
            .str_pad((string) ($this->day ?? 0), 2, '0', STR_PAD_LEFT);
    }

    public function toArray(): array
    {
        return [
            'year' => $this->year,
            'month' => $this->month,
            'day' => $this->day,
        ];
    }

    private function validate(): void
    {
        throw_if($this->year !== null && ($this->year < 1 || $this->year > 9999), InvalidArgumentException::class, 'Invalid year.');

        throw_if($this->month !== null && ($this->month < 1 || $this->month > 12), InvalidArgumentException::class, 'Invalid month.');

        throw_if($this->day !== null && ($this->day < 1 || $this->day > 31), InvalidArgumentException::class, 'Invalid day.');

        throw_if($this->day !== null && $this->month === null, InvalidArgumentException::class, 'Month is required when day is present.');

        throw_if($this->month !== null && $this->year === null, InvalidArgumentException::class, 'Year is required when month is present.');
    }
}
