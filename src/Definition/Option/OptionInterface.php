<?php
declare(strict_types=1);

namespace ExtendsFramework\Console\Shell\Definition\Option;

interface OptionInterface
{
    /**
     * Get option name.
     *
     * @return string
     */
    public function getName(): string;

    /**
     * Get option description.
     *
     * @return string
     */
    public function getDescription(): string;

    /**
     * Get short name.
     *
     * If empty, long name will be available.
     *
     * @return null|string
     */
    public function getShort(): ?string;

    /**
     * Get long name.
     *
     * If empty, short name will be available.
     *
     * @return null|string
     */
    public function getLong(): ?string;

    /**
     * Get if this option is a flag.
     *
     * @return bool
     */
    public function isFlag(): bool;

    /**
     * Get if this option can have multiple values.
     *
     * @return bool
     */
    public function isMultiple(): bool;
}
