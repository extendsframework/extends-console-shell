<?php
declare(strict_types=1);

namespace ExtendsFramework\Console\Shell\About;

interface AboutInterface
{
    /**
     * Get shell name.
     *
     * @return string
     */
    public function getName(): string;

    /**
     * Get program to run shell.
     *
     * @return string
     */
    public function getProgram(): string;

    /**
     * Get shell version.
     *
     * @return string
     */
    public function getVersion(): string;
}
