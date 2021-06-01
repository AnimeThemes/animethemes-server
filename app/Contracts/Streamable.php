<?php declare(strict_types=1);

namespace App\Contracts;

/**
 * Interface Streamable
 * @package App\Contracts
 */
interface Streamable
{
    /**
     * Get path.
     *
     * @return string
     */
    public function getPath(): string;

    /**
     * Get MIME type.
     *
     * @return string
     */
    public function getMimetype(): string;

    /**
     * Get size.
     *
     * @return int
     */
    public function getSize(): int;

    /**
     * Get name of storage disk.
     *
     * @return string
     */
    public function getDisk(): string;
}
