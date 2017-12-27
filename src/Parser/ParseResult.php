<?php
declare(strict_types=1);

namespace ExtendsFramework\Shell\Parser;

class ParseResult implements ParseResultInterface
{
    /**
     * Parsed data.
     *
     * @var array
     */
    protected $parsed;

    /**
     * Remaining data when not in strict mode.
     *
     * @var array
     */
    protected $remaining;

    /**
     * If parsing was done in strict mode.
     *
     * @var bool
     */
    protected $strict;

    /**
     * Create new parse result.
     *
     * @param array $parsed
     * @param array $remaining
     * @param bool  $strict
     */
    public function __construct(array $parsed, array $remaining, bool $strict)
    {
        $this->parsed = $parsed;
        $this->remaining = $remaining;
        $this->strict = $strict;
    }

    /**
     * @inheritDoc
     */
    public function getParsed(): array
    {
        return $this->parsed;
    }

    /**
     * @inheritDoc
     */
    public function getRemaining(): array
    {
        return $this->remaining;
    }

    /**
     * @inheritDoc
     */
    public function isStrict(): bool
    {
        return $this->strict;
    }
}
