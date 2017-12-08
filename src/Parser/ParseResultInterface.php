<?php
declare(strict_types=1);

namespace ExtendsFramework\Console\Shell\Parser;

interface ParseResultInterface
{
    /**
     * Get parsed data.
     *
     * @return array
     */
    public function getParsed(): array;

    /**
     * Get remaining data when not in strict mode.
     *
     * @return array
     */
    public function getRemaining(): array;

    /**
     * If parsing was done in strict mode.
     *
     * @return bool
     */
    public function isStrict(): bool;
}
